<?php

use Extension14v\Imagecredits14v\Command\CleanerCommandController;
use Extension14v\Imagecredits14v\Command\CleanerCommandControllerAdditionalFieldProvider;
use Extension14v\Imagecredits14v\Controller\AjaxController;
use Extension14v\Imagecredits14v\Controller\ImagelistController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

(static function(): void {
    ExtensionUtility::configurePlugin(
        'Imagecredits14v',
        'Imglist',
        [
            ImagelistController::class => 'list'
        ]
    );

    ExtensionUtility::configurePlugin(
        'Imagecredits14v',
        'Thumblist',
        [
            ImagelistController::class => 'thumbs'
        ],
        // non-cacheable actions
        [
            ImagelistController::class => 'thumbs'
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['changecopyrightinformation'] = AjaxController::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][CleanerCommandController::class] = [
        'extension' => 'imagecredits14v',
        'title' => 'ImageCredits Cleaner',
        'description' => 'Task zum bereinigen von Metadaten',
        'additionalFields' => CleanerCommandControllerAdditionalFieldProvider::class
    ];

    ExtensionManagementUtility::addTypoScript(
        'imagecredits14v',
        'setup',
        "@import 'EXT:imagecredits14v/Configuration/TypoScript/setup.typoscript'"
    );

    ExtensionManagementUtility::addTypoScriptSetup(
        trim('
            module.tx_imagecredits14v.settings < plugin.tx_imagecredits14v_imglist.settings
            module.tx_imagecredits14v.settings.paths.0 = /
        ')
    );
})();