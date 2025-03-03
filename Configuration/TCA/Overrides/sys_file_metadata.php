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
];

ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $tempColumns);
ExtensionManagementUtility::addToAllTCAtypes('sys_file_metadata', '--div--;Urheber-Informationen,tx_imagecredits14v_name, tx_imagecredits14v_link, tx_imagecredits14v_exlist, tx_imagecredits14v_term');
