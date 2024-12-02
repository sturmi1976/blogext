<?php


use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();


$pluginSignatureBloglist = 'blogext_bloglist';
$pluginSignatureBlogcategory = 'blogext_blogcategory';


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBloglist] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBlogcategory] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBloglist,
    'FILE:EXT:blogext/Configuration/Flexforms/Bloglist.xml'
);

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBlogcategory,
    'FILE:EXT:blogext/Configuration/Flexforms/Blogcategorymenu.xml'
);




(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Blogext',
        'bloglist',
        'Blog: Listenansicht'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Blogext',
        'blogcategory',
        'Blog: Kategorie-Menu'
    );
})();




 
// Add a field to tt_content for referencing
$GLOBALS['TCA']['tt_content']['columns']['tx_blogext_parent'] = [
    'exclude' => true,
    'label' => 'Parent Record from Blogext',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'foreign_table' => 'tx_blogext_domain_model_post',
        'default' => 0,
        'readOnly' => true,
    ],
];

$GLOBALS['TCA']['tt_content']['ctrl']['hideTable'] = true;