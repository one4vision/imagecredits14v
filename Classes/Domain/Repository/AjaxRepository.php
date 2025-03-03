<?php
namespace Extension14v\Imagecredits14v\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;


/**
 * The repository for Ajax
 */
class AjaxRepository
{
    public function saveMetaDataValue(int $metaUid, string $fieldName, $fieldValue): bool
    {
        $table = 'sys_file_metadata';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->update($table)
            ->where($queryBuilder->expr()->eq('uid', $metaUid))->set($fieldName, $fieldValue)->executeStatement();

        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->persistAll();

        return true;
    }

}
