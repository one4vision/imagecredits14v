<?php

declare(strict_types=1);

namespace Extension14v\Imagecredits14v\Domain\Repository;

use Extension14v\Imagecredits14v\Domain\Model\FileReference;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

class FileReferenceRepository extends Repository
{
    protected array $availableTables = [];
    public function findReferencesByPages(array $pages=[], bool $isBE=true, array $extensions=[]): array {
        if($pages === []) {
            return [];
        }
        $query  = $this->createQuery();
        if($isBE) {
            $constraints = [];
            $constraints[] = $query->equals('deleted', 0);
            $constraints[] = $query->in('pid', $pages);
        } else {
            $extensions[] = 'tt_content';
            $extensions[] = 'pages';
            $constraints = [];
            $constraints[] = $query->equals('deleted', 0);
            $constraints[] = $query->in('tablenames', $extensions);
        }
        $references = $query->matching($query->logicalAnd(...$constraints))->execute();
        $this->collectAvailableTableNames();
        return array_filter(
            $references->toArray(),
            function(FileReference $reference) {
                if(in_array($reference->getTablenames(), $this->availableTables, true)) {
                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
                        $reference->getTablenames()
                    );
                    $counter = $queryBuilder
                        ->count('*')
                        ->from($reference->getTablenames())
                        ->where(
                            $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($reference->getUidForeign(), \PDO::PARAM_INT))
                        )
                        ->executeQuery()
                        ->fetchOne();
                    return $counter > 0;
                }
            }
        );
    }

    private function collectAvailableTableNames(): void {
        $tableNames = [];
        foreach($GLOBALS['TCA'] as $tableName => $data) {
            if(!in_array($tableName, $tableNames, true)) {
                $tableNames[] = $tableName;
            }
        }
        $this->availableTables = $tableNames;
    }
}