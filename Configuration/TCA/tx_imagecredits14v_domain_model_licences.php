<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_domain_model_licences',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'name,licence_name,licence_url',
        'iconfile' => 'EXT:imagecredits14v/Resources/Public/Icons/tx_imagecredits14v_domain_model_licences.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name,licence_name,licence_url, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, sys_language_uid, l10n_parent, l10n_diffsource, hidden'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language'],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_imagecredits14v_domain_model_licences',
                'foreign_table_where' => 'AND tx_imagecredits14v_domain_model_licences.pid=###CURRENT_PID### AND tx_imagecredits14v_domain_model_licences.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],

        'name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_domain_model_licences.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'licence_name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_domain_model_licences.licence_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'licence_url' => [
            'exclude' => false,
            'label' => 'LLL:EXT:imagecredits14v/Resources/Private/Language/locallang_db.xlf:tx_imagecredits14v_domain_model_licences.licence_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
    
    ],
];
