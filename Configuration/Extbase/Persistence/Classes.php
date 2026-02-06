<?php
declare(strict_types = 1);

use Extension14v\Imagecredits14v\Domain\Model\Category;
use Extension14v\Imagecredits14v\Domain\Model\FileReference;

return [
    Category::class => [
        'tableName' => 'sys_category',
    ],

    \TYPO3\CMS\Extbase\Domain\Model\Category::class => [
        'subclasses' => [
            Category::class,
        ]
    ],

    FileReference::class => [
        'tableName' => 'sys_file_reference',
    ]
];
