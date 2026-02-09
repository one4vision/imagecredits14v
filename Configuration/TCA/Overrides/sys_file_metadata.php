<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$tempColumns = [
    'tx_imagecredits14v_name' => [
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_name',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'max' => 255,
        ]
    ],
    'tx_imagecredits14v_link' => [
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_link',
        'config' => [
            'type' => 'input',
            'placeholder' => 'https://',
            'size' => 30,
            'max' => 255,
        ]
    ],
    'tx_imagecredits14v_exlist' => [
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_exlist',
        'config' => [
            'type' => 'check'
        ]
    ],
    'tx_imagecredits14v_term' => [
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_term',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => '- keine Angabe -','value' => 0]
            ],
            'foreign_table' => 'tx_imagecredits14v_domain_model_licences'
        ]
    ],
    'caption' => [
        'exclude' => true,
        'l10n_mode' => 'prefixLangTitle',
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.caption',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 3,
        ],
    ],
    'download_name' => [
        'exclude' => true,
        'l10n_mode' => 'exclude',
        'l10n_display' => 'defaultAsReadonly',
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.download_name',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
            'max' => 255,
        ],
    ],
    'copyright' => [
        'exclude' => true,
        'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.copyright',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 3,
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $tempColumns);
ExtensionManagementUtility::addToAllTCAtypes('sys_file_metadata', 'caption, download_name, copyright, --div--;Urheber-Informationen,tx_imagecredits14v_name, tx_imagecredits14v_link, tx_imagecredits14v_exlist, tx_imagecredits14v_term');
