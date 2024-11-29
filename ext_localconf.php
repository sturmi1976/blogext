<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Lanius\Blogext\Backend\Controller\BlogBackendController;


(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
       'Blogext',
       'bloglist',
       [
             \Lanius\Blogext\Controller\BlogController::class => 'list, show, category' 
       ]
    );
 })();




// Typoscript global laden
ExtensionManagementUtility::addTypoScript(
    'blogext',
    'setup',
    "@import 'EXT:Blogext/Configuration/Sets/T3blog/setup.typoscript'",
);
