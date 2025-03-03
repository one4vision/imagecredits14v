<?php

namespace Extension14v\Imagecredits14v\Domain\Repository;

use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use Doctrine\DBAL\Driver\Exception;
use Extension14v\Imagecredits14v\Domain\Model\FileReference;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class o4vRepository
{
    /**
     * @var array
     */
    protected array $rootLineChecked = [];
    /**
     * @var array
     */
    protected array $pagesInTrash = [];

    public function collectFilesFromReferences(array $references, array $settings, int $selectedType, array $excludeTypes, string $area='BE', array $fePaths=[], array $pidList=[], array $pagesToIgnore=[]): array {
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $result = [];
        $referenceUidList = [];
        foreach($references as $reference) {
            /** @var FileReference $reference */
            $uidLocal = $reference->getUidLocal();
            $tableName = $reference->getTablenames();
            $pid = $reference->getPid();
            $isInTrash = $this->pageIsInTrash($pid);
            if(!$isInTrash) {
                if ($area === 'FE') {
                    $useReference = false;
                    $referencePid = (int) $pid;
                    if(!in_array($referencePid, $pagesToIgnore, true)) {
                        if ($tableName === 'tt_address' || str_starts_with($tableName, 'tx_')) {
                            if (in_array($pid, $pidList, true)) {
                                $useReference = true;
                            }
                        } elseif (in_array($pid, $pidList, true)) {
                            $useReference = true;
                        }
                    }
                } else {
                    $useReference = true;
                }

                if ($useReference) {
                    $result[$uidLocal]['references'][] = $reference;
                    $fileObject = $resourceFactory->getFileObject($reference->getUidLocal());
                    $result[$uidLocal]['fileObjects'][] = $fileObject;
                    if (!in_array($uidLocal, $referenceUidList, true)) {
                        $referenceUidList[] = $uidLocal;
                    }
                }
            }
        }

        if($referenceUidList !== []) {
            $allowedTypes = GeneralUtility::intExplode(',', $settings['contentTypes'], true);
            $paths = (is_array($settings['paths']) ? $settings['paths'] : []);

            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $files = $queryBuilder->select('*')
                ->from('sys_file')
                ->where($queryBuilder->expr()->in('uid', $referenceUidList), $queryBuilder->expr()->eq('type', $selectedType))->executeQuery();
            while ($row = $files->fetchAssociative()) {
                $uid = (int)$row['uid'];
                $storage = (int)$row['storage'];
                $type = (int)$row['type'];
                $identifier = (string)$row['identifier'];
                $hash = (string)$row['identifier_hash'];
                $name = (string)$row['name'];
                $sha1 = (string)$row['sha1'];
                $size = (int)$row['size'];
                $created = (int)$row['creation_date'];
                $modified = (int)$row['modification_date'];
                $extension = strtolower((string) $row['extension']);
                $missing = (int) $row['missing'];

                if($missing === 0) {
                    if ($excludeTypes !== [] && in_array($extension, $excludeTypes, true)) {
                        continue;
                    }

                    if ($area === 'FE' && $fePaths !== []) {
                        $hasPath = 0;
                        foreach ($fePaths as $storageUid => $fePathList) {
                            foreach ($fePathList as $fePath) {
                                if (!str_starts_with((string) $fePath, '/')) {
                                    $fePath = '/' . $fePath;
                                }
                                if ($storageUid === $storage && str_starts_with($identifier, (string) $fePath)) {
                                    $hasPath++;
                                }
                            }
                        }
                        if ($hasPath === 0) {
                            continue;
                        }
                    }

                    $modificationDate = new \DateTime();
                    $modificationDate->setTimezone(new \DateTimeZone('Europe/Berlin'));
                    $modificationDate->setTimestamp($modified);

                    $creationDate = new \DateTime();
                    $creationDate->setTimezone(new \DateTimeZone('Europe/Berlin'));
                    $creationDate->setTimestamp($created);

                    if (in_array($type, $allowedTypes, true)) {
                        $foldersFound = 0;
                        if ($area === 'FE') {
                            $foldersFound++;
                        } else {
                            foreach ($paths as $path) {
                                if (str_starts_with($identifier, (string) $path)) {
                                    $foldersFound++;
                                }
                            }
                        }

                        if ($foldersFound > 0) {
                            $metadata = $this->getFileMetaData($uid);
                            $showInList = $metadata !== null;
                            if (array_key_exists('tx_imagecredits14v_exlist', $metadata)) {
                                $showInList = (int)$metadata['tx_imagecredits14v_exlist'] === 0;
                            }
                            if ($showInList) {
                                $result[$uid]['file'] = [
                                    'uid' => $uid,
                                    'storage' => $storage,
                                    'type' => $type,
                                    'metadata' => $metadata,
                                    'identifier' => $identifier,
                                    'hash' => $hash,
                                    'name' => $name,
                                    'sha1' => $sha1,
                                    'size' => $size,
                                    'created' => $creationDate,
                                    'modified' => $modificationDate,
                                    'folder' => str_replace($name, '', $identifier),
                                    'missing' => $missing
                                ];


                                $editLink = '#';
                                if (($area === 'BE') && array_key_exists('uid', $metadata)) {
                                    $urlParameters = [
                                        'edit' => [
                                            'sys_file_metadata' => [
                                                $metadata['uid'] => 'edit'
                                            ]
                                        ],
                                        'columnsOnly' => '',
                                        'createExtension' => 0,
                                        'returnUrl' => GeneralUtility::getIndpEnv(
                                                'REQUEST_URI'
                                            ) . '&tx_imagecredits14v_web_imagecredits14vimagecredits%5BcontentType%5D=' . $selectedType
                                    ];
                                    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
                                    $editLink = $uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
                                }
                                $result[$uid]['edit'] = $editLink;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return int[]|string
     */
    public function getPidTree(int $parent, int $depth, bool $asArray=true, bool $getDoktype=false, $justPages=0): array|string
    {
        $childPidList = $getDoktype ? $this->getTreeListDoktype([], $parent) : $this->getTreeList($parent, $depth, 0, '', $justPages);
        if($asArray) {
            $childPidList = GeneralUtility::intExplode(',', $childPidList, true);
        }
        return $childPidList;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTreeList(int $id, int $depth, int $begin = 0, string $permsClause = '', $useDokType=0): string
    {
        if ($id < 0) {
            $id = \abs($id);
        }
        $theList = $begin === 0 ? $id : '';
        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                );
            if($permsClause !== '') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permsClause));
            }
            if($useDokType) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('doktype', 1));
            }
            $statement = $queryBuilder->execute();
            while ($row = $statement->fetchAssociative()) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }
                if ($depth > 1) {
                    $theList .= $this->getTreeList($row['uid'], $depth - 1, $begin - 1, $permsClause);
                }
            }
        }
        return (string) $theList;
    }

    /**
     * @param array $theList
     * @param int $id
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTreeListDoktype(array $theList, int $id): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder->select('uid','pid','doktype')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', 0)
            );
        $statement = $queryBuilder->execute();
        while ($row = $statement->fetchAssociative()) {
            $pid = (int) $row['pid'];
            $theList[] = $row;
            if($pid > 0) {
                $theList = $this->getTreeListDoktype($theList, $pid);
            }
        }
        return $theList;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function buildReferencePages($imageList): array {
        $images = [];
        foreach($imageList as $k => $image) {
            $references = $image['references'];
            $pages = [];
            foreach($references as $reference) {
                /** @var FileReference $reference */
                $tableName = $reference->getTablenames();
                $pageUid = $reference->getPid();
                if(!array_key_exists($pageUid, $pages)) {
                    $pageData = self::getPage($pageUid);
                    if($pageData) {
                        $pages[$pageUid] = [
                            'uid' => $pageUid,
                            'title' => $pageData['title'],
                            'isPage' => (int) $pageData['doktype'] === 1
                        ];
                    }
                }
            }
            $image['pages'] = $pages;
            $images[$k] = $image;
        }

        return $images;
    }

    /**
     * @return array<int|string, array{references: mixed, fileObjects: mixed, edit: mixed, file: mixed}>
     */
    public function moveLizenzToFile($images, $licences): array {
        $newList = [];
        foreach($images as $k => $image) {
            $newList[$k]['references'] = $image['references'];
            $newList[$k]['fileObjects'] = $image['fileObjects'];
            $newList[$k]['edit'] = $image['edit'];

            $file = $image['file'];
            $metadata = $file['metadata'];
            $file['licence'] = null;

            $fileName = $file['name'];
            $strippedName = '';
            if(strlen($fileName) > 95) {
                $firstLetters = substr($fileName, 0, 45);
                $lastLetters = substr($fileName, -45);
                $strippedName = $firstLetters.'...'.$lastLetters;
            }
            $file['strippedName'] = $strippedName;

            if(array_key_exists('tx_imagecredits14v_term', $metadata)) {
                $licenceUid = (int) $metadata['tx_imagecredits14v_term'];
                if($licenceUid > 0 && array_key_exists($licenceUid, $licences)) {
                    $file['licence'] = $licences[$licenceUid];
                }
            }
            $newList[$k]['file'] = $file;
        }
        return $newList;
    }

    private function getFileMetaData(int $fileUid) {
        $metaDataRepository = GeneralUtility::makeInstance(MetaDataRepository::class);
        return $metaDataRepository->findByFileUid($fileUid);
    }

    public static function getPage($pageUid) {
        return GeneralUtility::makeInstance(PageRepository::class)->getPage($pageUid);
    }

    public function pageIsInTrash($pageUid): bool {
        if(in_array($pageUid, $this->pagesInTrash, true)) {
            return true;
        }
        if(!in_array($pageUid, $this->rootLineChecked, true)) {
            $this->rootLineChecked[] = $pageUid;
            $rootLine = $this->getTreeListDoktype([], $pageUid);
            foreach($rootLine as $item) {
                if($item['doktype'] === 255) {
                    $this->pagesInTrash[] = $pageUid;
                    return true;
                }
            }
        }
        return false;
    }
}