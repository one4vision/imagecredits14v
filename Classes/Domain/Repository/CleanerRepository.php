<?php

namespace Extension14v\Imagecredits14v\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanerRepository
{
    public function cleanupCreator($collection): void
    {
        if(\count($collection) > 0) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_metadata');
            foreach($collection as $item) {
                $sql = "UPDATE sys_file_metadata set creator = trim(regexp_replace(creator,'((^| )".$item.")+( |$)',' '))";
                $connection->executeQuery($sql);
            }
        }
    }
}