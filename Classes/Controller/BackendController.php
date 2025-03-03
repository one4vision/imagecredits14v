<?php
namespace Extension14v\Imagecredits14v\Controller;

use Psr\Http\Message\ResponseInterface;
use Extension14v\Imagecredits14v\Domain\Repository\FileReferenceRepository;
use Extension14v\Imagecredits14v\Domain\Repository\LicencesRepository;
use Extension14v\Imagecredits14v\Domain\Repository\o4vRepository;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/***
 *
 * This file is part of the "Imagecredits14v" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Oliver Busch <ob@14v.de>, one4vision GmbH
 *
 ***/

/**
 * BackendController
 */
class BackendController extends ActionController
{
    protected ?o4vRepository $o4vRepository = null;

    protected ?FileReferenceRepository $fileReferenceRepository = null;

    protected ?LicencesRepository $licencesRepository = null;

    protected ?ModuleData $moduleData = null;
    protected ModuleTemplate $moduleTemplate;
    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected IconFactory $iconFactory;
    protected PageRenderer $pageRenderer;

    /**
     * @var array
     */
    protected $settings = [];

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function injectIconFactory(IconFactory $iconFactory): void
    {
        $this->iconFactory = $iconFactory;
    }

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    public function injectO4vRepository(o4vRepository $o4vRepository): void {
        $this->o4vRepository = $o4vRepository;
    }

    public function injectFileReferenceRepository(FileReferenceRepository $fileReferenceRepository): void {
        $this->fileReferenceRepository = $fileReferenceRepository;
    }

    public function injectLicenceRepository(LicencesRepository $licencesRepository): void {
        $this->licencesRepository = $licencesRepository;
    }

    public function initializeAction(): void
    {
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        $this->moduleData = $this->request->getAttribute('moduleData');
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $searchPageId = 0;
        $pageTitle = 'Seite';
        $useContentTypes = GeneralUtility::intExplode(',', $this->settings['contentTypes'], true);
        $excludeTypes = GeneralUtility::trimExplode(',', $this->settings['excludeFormates'], true);

        $contentType = 2;
        $contentTypeList = [];
        $contentTypeNames = [
            0 => 'unbekannter Dateityp',
            1 => 'Dateityp: Text',
            2 => 'Dateityp: Bild',
            3 => 'Dateityp: Audio',
            4 => 'Dateityp: Video',
            5 => 'Dateityp: Application'
        ];

        for($c=0, $cMax = count($contentTypeNames); $c< $cMax; $c++) {
            if(in_array($c, $useContentTypes, true)) {
                $contentTypeList[$c] = $contentTypeNames[$c];
            }
        }

        if($this->request->hasArgument('contentType')) {
            $contentType = (int)$this->request->getArgument('contentType');
        }

        if(isset($_GET['id'])) {
            $searchPageId = (int)$_GET['id'];
            if($searchPageId > 0) {
                $pageTitle = $this->o4vRepository::getPage($searchPageId)['title'];
            }
        }
        $imageList = [];
        $isPage = false;
        if($searchPageId > 0) {
            $pidList = $this->o4vRepository->getPidTree($searchPageId, 9999);
            if($pidList !== []) {
                $fileReferences = $this->fileReferenceRepository->findReferencesByPages($pidList, true);
                if($fileReferences !== []) {
                    $fileList = $this->o4vRepository->collectFilesFromReferences(
                        $fileReferences,
                        $this->settings,
                        $contentType,
                        $excludeTypes,
                        'BE',
                        [],
                        $pidList
                    );
                    ksort($fileList);
                    $imageList = $this->cleanUpImageList($fileList);
                    $isPage = true;
                }
            }
        }

        $licences = $this->licencesRepository->findAllLicences();
        $licences = $this->licencesRepository->licencesToKeyedArray($licences);
        $imageList = $this->o4vRepository->moveLizenzToFile($imageList, $licences);

        $this->moduleTemplate->assignMultiple([
            'images' => $imageList,
            'pageTitle' => $pageTitle,
            'pageUid' => $searchPageId,
            'contentType' => $contentType,
            'contentTypeList' => $contentTypeList,
            'isPage' => $isPage
        ]);

        $this->moduleTemplate->setTitle('Datei-Metadaten');
        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $searchPageId]);
        $this->addButtons();
        return $this->moduleTemplate->renderResponse('Backend/List');
    }

    public function licenceAction(): ResponseInterface {
        $licences = $this->licencesRepository->findAllLicences();
        $licences = $this->licencesRepository->expandLicences($licences);
        $addLink = $this->licencesRepository->createBackendLink('new','tx_imagecredits14v_domain_model_licences',0);
        $this->moduleTemplate->assignMultiple([
            'licences' => $licences,
            'addLink' => $addLink
        ]);
        $this->moduleTemplate->setTitle('Datei-Metadaten');
        $this->addButtons();
        return $this->moduleTemplate->renderResponse('Backend/Licence');
    }

    /**
     * @return array[]
     */
    private function cleanUpImageList(array $imageList): array {
        $imageList = array_values($imageList);
        $cleanedList = [];
        foreach($imageList as $item) {
            if(array_key_exists('file', $item)) {
                $cleanedList[] = $item;
            }
        }
        return $cleanedList;
    }

    protected function addButtons(): void {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $linkUrl = $this->uriBuilder->uriFor('list');
        $licenceUrl = $this->uriBuilder->uriFor('licence');
        $listBtn = $buttonBar->makeLinkButton()
            ->setHref($linkUrl)
            ->setTitle('Übersicht aller Dateien (mit Referenzangaben)')
            ->setShowLabelText('Link zur Übersicht')
            ->setIcon($this->iconFactory->getIcon('actions-list', Icon::SIZE_SMALL));
        $licenceBtn = $buttonBar->makeLinkButton()
            ->setHref($licenceUrl)
            ->setTitle('Bild-Lizenzen')
            ->setShowLabelText('Bild-Lizenzen')
            ->setIcon($this->iconFactory->getIcon('actions-certificate-alternative', Icon::SIZE_SMALL));
        $buttonBar->addButton($listBtn, ButtonBar::BUTTON_POSITION_LEFT, 1);
        $buttonBar->addButton($licenceBtn, ButtonBar::BUTTON_POSITION_LEFT, 2);
    }

}
