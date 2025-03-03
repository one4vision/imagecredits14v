<?php

use Extension14v\Imagecredits14v\Controller\BackendController;

return [
    'web_Imagecredits14vImagecredits' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/imagecredits',
        'labels' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_imagecredits14v.xlf',
        'iconIdentifier' => 'module-imagecredits',
        'extensionName' => 'Imagecredits14v',
        'controllerActions' => [
            BackendController::class => ['list', 'export', 'licence'],
        ]
    ],
];
