<?php
namespace Extension14v\Imagecredits14v\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class ForeignContentReferences {
    public function getForeignContents(&$params): void {
        $ignoreInList = ['pages','tt_content'];
        $table = 'sys_file_reference';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $result = $queryBuilder->select('tablenames')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('deleted', 0),
                $queryBuilder->expr()->eq('hidden', 0)
            )
            ->groupBy('tablenames')
            ->executeQuery();
        while($row = $result->fetchAssociative()) {
            $name = $row['tablenames'];
            if(isset($GLOBALS['TCA'][$name]) && !in_array($name, $ignoreInList, true)) {
                $tableTitle = $GLOBALS['TCA'][$name]['ctrl']['title'];
                if(str_starts_with((string) $tableTitle, 'LLL:EXT')) {
                    $tableTitle = LocalizationUtility::translate($tableTitle);
                }
                $params['items'][] = [$tableTitle, $name];
            }
        }
    }
}
