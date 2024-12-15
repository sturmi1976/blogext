<?php


use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();


$pluginSignatureBloglist = 'blogext_bloglist';
$pluginSignatureBlogcategory = 'blogext_blogcategory';
$pluginSignatureBlogrss = 'blogext_blogrss';
$pluginSignatureBlogtag = 'blogext_blogtag';
$pluginSignatureBloglast = 'blogext_bloglast';


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBloglist] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBlogcategory] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBlogrss] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBlogtag] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureBloglast] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBloglist,
    'FILE:EXT:blogext/Configuration/Flexforms/Bloglist.xml'
);

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBlogcategory,
    'FILE:EXT:blogext/Configuration/Flexforms/Blogcategorymenu.xml'
);

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBlogrss,
    'FILE:EXT:blogext/Configuration/Flexforms/Blogrss.xml'
);

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBlogtag,
    'FILE:EXT:blogext/Configuration/Flexforms/Tag.xml'
);

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignatureBloglast,
    'FILE:EXT:blogext/Configuration/Flexforms/Last.xml'
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
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Blogext',
        'blogrss',
        'Blog: RSS Feed'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Blogext',
        'blogtag',
        'Blog: Tag-Cloud'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Blogext',
        'bloglast',
        'Blog: Letzte Blogartikel'
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