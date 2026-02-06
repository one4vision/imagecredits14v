<?php
namespace Extension14v\Imagecredits14v\Controller;

use Psr\Http\Message\ResponseInterface;
use Extension14v\Imagecredits14v\Domain\Repository\FileReferenceRepository;
use Extension14v\Imagecredits14v\Domain\Repository\LicencesRepository;
use Extension14v\Imagecredits14v\Domain\Repository\o4vRepository;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ImagelistController extends ActionController
{
    protected $settings = [];
    protected array $licences = [];
    protected ?FileRepository $fileRepository = null;
    protected ?o4vRepository $o4vRepository = null;
    protected ?LicencesRepository $licencesRepository = null;
    protected ?FileReferenceRepository $fileReferenceRepository = null;
    protected IconFactory $iconFactory;

    public function injectIconFactory(IconFactory $iconFactory): void
    {
        $this->iconFactory = $iconFactory;
    }

    public function injectO4vRepository(o4vRepository $o4vRepository): void {
        $this->o4vRepository = $o4vRepository;
    }

    public function injectFileReferenceRepository(FileReferenceRepository $fileReferenceRepository): void {
        $this->fileReferenceRepository = $fileReferenceRepository;
    }

    public function injectLicencesRepository(LicencesRepository $licencesRepository): void {
        $this->licencesRepository = $licencesRepository;
    }

    public function initializeAction(): void {
        $startSettings = $this->settings;
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT, 'imagecredits14v'
        );
        $settings = GeneralUtility::removeDotsFromTS($settings);
        $this->settings = $settings['plugin']['tx_imagecredits14v_imglist']['settings'];
        $this->settings = array_merge($this->settings, $startSettings);
        $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);

        $this->settings['paths'] = [];

        if(!array_key_exists('directories', $this->settings)) {
            $this->settings['directories'] = '';
        }

        if(!array_key_exists('extensions', $this->settings)) {
            $this->settings['extensions'] = '';
        }

        if(!array_key_exists('showImages', $this->settings)) {
            $this->settings['showImages'] = 1;
        } else {
            $this->settings['showImages'] = (int) $this->settings['showImages'];
        }

        if(!array_key_exists('currentPage', $this->settings)) {
            $this->settings['currentPage'] = 0;
        } else {
            $this->settings['currentPage'] = (int) $this->settings['currentPage'];
        }

        if(!array_key_exists('justPagedImages', $this->settings)) {
            $this->settings['justPagedImages'] = 1;
        } else {
            $this->settings['justPagedImages'] = (int) $this->settings['justPagedImages'];
        }

        if(!array_key_exists('ignore', $this->settings)) {
            $this->settings['ignore'] = '';
        }

        if(!array_key_exists('news', $this->settings)) {
            $this->settings['news'] = ['detailPid' => ''];
        }

        if(!array_key_exists('feGroups', $this->settings)) {
            $this->settings['feGroups'] = '';
        }

        $licences = $this->licencesRepository->findAllLicences();
        $this->licences = $this->licencesRepository->licencesToKeyedArray($licences);
    }

    public function listAction(): ResponseInterface {
        $this->loadStyling();
        $contentType = 2;
        $excludeTypes = GeneralUtility::trimExplode(',', $this->settings['excludeFormates'], true);
        $directories = GeneralUtility::trimExplode(',', $this->settings['directories'], true);
        $ignorePages = GeneralUtility::intExplode(',', $this->settings['ignore'], true);
        $extensions = GeneralUtility::trimExplode(',', $this->settings['extensions'], true);
        if($this->settings['currentPage'] === 1) {
            $pidList = [(int)$GLOBALS['TSFE']->id];
        } else {
            $pidList = $this->o4vRepository->getPidTree($this->settings['rootpage'], 999, true, false, (int) $this->settings['justPagedImages']);
        }
        $imageList = [];
        $paths = ($directories !== [] ? $this->buildFilePaths($directories) : []);
        $pagesToIgnore = $this->collectPagesToIgnore($ignorePages);
        if($pidList !== []) {
            $fileReferences = $this->fileReferenceRepository->findReferencesByPages($pidList, false, $extensions);
            if($fileReferences !== []) {
                $fileList = $this->o4vRepository->collectFilesFromReferences(
                    $fileReferences,
                    $this->settings,
                    $contentType,
                    $excludeTypes,
                    'FE',
                    $paths,
                    $pidList,
                    $pagesToIgnore
                );
                ksort($fileList);
                $imageList = $this->cleanUpImageList($fileList);
                $imageList = $this->o4vRepository->buildReferencePages($imageList);
            }
        }
        $this->view->assign('images', $imageList);
        $this->view->assign('licences', $this->licences);
        return $this->htmlResponse();
    }

    public function thumbsAction(): ResponseInterface
    {
        $this->loadStyling();
        $isEditable = $this->checkFeGroupAccess($this->request, $this->settings['feGroups']);
        $contentType = 2;
        $excludeTypes = GeneralUtility::trimExplode(',', $this->settings['excludeFormates']);
        $directories = GeneralUtility::trimExplode(',', $this->settings['directories'], true);
        $ignorePages = GeneralUtility::intExplode(',', $this->settings['ignore'], true);
        $extensions = GeneralUtility::trimExplode(',', $this->settings['extensions'], true);
        $pidList = $this->o4vRepository->getPidTree($this->settings['rootpage'], 999);
        $imageList = [];
        $paths = ($directories !== [] ? $this->buildFilePaths($directories) : []);
        $pagesToIgnore = $this->collectPagesToIgnore($ignorePages);
        if($pidList !== []) {
            $fileReferences = $this->fileReferenceRepository->findReferencesByPages($pidList, false, $extensions);
            if($fileReferences !== []) {
                $fileList = $this->o4vRepository->collectFilesFromReferences(
                    $fileReferences,
                    $this->settings,
                    $contentType,
                    $excludeTypes,
                    'FE',
                    $paths,
                    $pidList,
                    $pagesToIgnore
                );
                ksort($fileList);
                $imageList = $this->cleanUpImageList($fileList);
                $imageList = $this->o4vRepository->buildReferencePages($imageList);
            }
        }

        $this->view->assign('images', $imageList);
        $this->view->assign('isEditable', $isEditable);
        $this->view->assign('licences', $this->licences);
        return $this->htmlResponse();
    }

    public function loadStyling() {
        $jsFile = 'EXT:imagecredits14v/Resources/Public/JavaScript/ImageCredits14v.js';
        $lightboxFile = 'EXT:imagecredits14v/Resources/Public/JavaScript/Lightbox.js';
        $cssFile = 'EXT:imagecredits14v/Resources/Public/Css/ImageCredits14v.css';
        $assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
        $assetCollector->addJavaScript('imageCreditJs', $jsFile);
        $assetCollector->addJavaScript('imageCreditsLightboxJs', $lightboxFile);
        $assetCollector->addStyleSheet('imageCreditCss', $cssFile);
    }

    /**
     * @return array[]
     */
    private function cleanUpImageList(array $imageList): array {
        $imageList = array_values($imageList);
        $cleanedList = [];
        foreach($imageList as $item) {
            if(array_key_exists('file', $item)) {
                $fileUid = (int) $item['file']['uid'];
                $cleanedList[$fileUid] = $item;
            }
        }
        krsort($cleanedList);
        return $cleanedList;
    }

    private function buildFilePaths($paths): array {
        $build = [];
        foreach($paths as $path) {
            $pathParts = GeneralUtility::trimExplode(':/', $path);
            $storage = (int) $pathParts[0];
            $folder = $pathParts[1];
            $build[$storage][] = $folder;
        }
        return $build;
    }

    /**
     * @param array $rootPages
     * @return array
     */
    private function collectPagesToIgnore(array $rootPages): array
    {
        $pagesList = [];
        foreach($rootPages as $rootPage) {
            if(!in_array($rootPage, $pagesList, true)) {
                $pagesList[] = $rootPage;
            }
            $subPages = $this->o4vRepository->getPidTree($rootPage,999);
            if($subPages) {
                foreach($subPages as $subPage) {
                    $subPage = (int) $subPage;
                    if(!in_array($subPage, $pagesList, true)) {
                        $pagesList[] = $subPage;
                    }
                }
            }
        }
        return $pagesList;
    }

    private function checkFeGroupAccess($request, string $feGroups=''): bool {
        if($GLOBALS['BE_USER']) {
            return true;
        }
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = $request->getAttribute('frontend.user');
        if($frontendUser->getUserId() && $frontendUser->getUserId() > 0) {
            $userGroups = GeneralUtility::intExplode(',', $frontendUser->user['usergroup'], true);
            $possibleGroups = GeneralUtility::intExplode(',', $feGroups, true);
            if(\count($possibleGroups) > 0) {
                foreach($possibleGroups as $group) {
                    if(in_array($group, $userGroups, true)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
