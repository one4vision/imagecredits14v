<?php

declare(strict_types=1);

namespace Extension14v\Imagecredits14v\Domain\Repository;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Extension14v\Imagecredits14v\Domain\Model\Licences;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LicencesRepository extends Repository
{
    public function findAllLicences()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('pid', 0));
        return $query->execute();
    }

    public function expandLicences($licences) {
        foreach($licences as $licence) {
            /** @var Licences $licence */
            $editLink = $this->createBackendLink('edit', 'tx_imagecredits14v_domain_model_licences', $licence->getUid());
            $licence->setEditLink($editLink);
        }
        return $licences;
    }

    /**
     * @return array<(string | int), Licences>
     */
    public function licencesToKeyedArray($licences): array {
        $newList = [];
        foreach($licences as $licence) {
            /** @var Licences $licence */
            $newList[$licence->getUid()] = $licence;
        }
        return $newList;
    }

    /**
     * @throws RouteNotFoundException
     */
    public function createBackendLink($action, $table, $uid, $columnsOnly = '', $defaultValues = [], $returnUrl = '') {
        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $urlParameters = [
            'edit' => [
                $table => [
                    $uid => $action
                ]
            ],
            'columnsOnly' => $columnsOnly,
            'createExtension' => 0,
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ];
        if($defaultValues !== []) {
            $urlParameters['defVals'] = $defaultValues;
        }
        return $backendUriBuilder->buildUriFromRoute('record_edit', $urlParameters);
    }
}
