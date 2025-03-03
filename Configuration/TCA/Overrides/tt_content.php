<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionUtility::registerPlugin(
    'Imagecredits14v',
    'Imglist',
    'Image-Credits :: Copyright'
);

ExtensionUtility::registerPlugin(
    'Imagecredits14v',
    'Thumblist',
    'Image-Credits :: Metadata'
);

$extensionName = strtolower(GeneralUtility::underscoredToUpperCamelCase('imagecredits14v'));
$pluginName = strtolower('Imglist');
$pluginSignature = $extensionName.'_'.$pluginName;

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:imagecredits14v/Configuration/FlexForms/flexform_imagelist.xml');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,pages,recursive';

$pluginName2 = strtolower('Thumblist');
$pluginSignature2 = $extensionName.'_'.$pluginName2;

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature2] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature2, 'FILE:EXT:imagecredits14v/Configuration/FlexForms/flexform_imagethumbs.xml');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature2] = 'select_key,pages,recursive';