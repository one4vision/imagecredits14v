<?php
namespace Extension14v\Imagecredits14v\Command;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class CleanerCommandController extends AbstractTask {
    public string $clean_creator = '';
    public function execute(): bool
    {
        $clean_creator = trim($this->clean_creator);
        if($clean_creator !== '') {
            $this->cleanupCreator(GeneralUtility::trimExplode(',', $clean_creator, true));
        }
        return true;
    }

    public function cleanupCreator($collection): void
    {
        if(\count($collection) > 0) {
            $connectionPool = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);
            $connection = $connectionPool->getConnectionForTable('sys_file_metadata');
            foreach($collection as $item) {
                $sql = "UPDATE sys_file_metadata set creator = trim(regexp_replace(creator,'((^| )".$item.")+( |$)',' '))";
                $connection->executeQuery($sql);
            }
        }
    }
}