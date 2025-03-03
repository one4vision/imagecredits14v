<?php
declare(strict_types = 1);

return [
    \Extension14v\Imagecredits14v\Domain\Model\Category::class => [
        'tableName' => 'sys_category',
    ],

    \TYPO3\CMS\Extbase\Domain\Model\Category::class => [
        'subclasses' => [
            \Extension14v\Imagecredits14v\Domain\Model\Category::class,
        ]
    ],

    \Extension14v\Imagecredits14v\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
    ]
];
