<?php

defined('TYPO3') or die();


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Lanius\Blogext\Backend\Controller\BlogBackendController;


(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
       'Blogext',
       'bloglist',
       [
             \Lanius\Blogext\Controller\BlogController::class => 'list, show, category, writeComment' 
       ]
    );
 })();

 (function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
       'Blogext',
       'blogcategory',
       [
             \Lanius\Blogext\Controller\CategorymenuController::class => 'list'   
       ]
    );
 })();

 (function () {
   \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
      'Blogext',
      'blogrss',
      [
            \Lanius\Blogext\Controller\FeedController::class => 'rss'   
      ]
   );
})();




// CSS for backend
$GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['blogext'] = 'EXT:blogext/Resources/Public/Css/Backend/backend.css?'.time();

 


ExtensionManagementUtility::addTypoScript(
    'blogext',
    'setup',
    "@import 'EXT:Blogext/Configuration/Sets/T3blog/setup.typoscript'",
);
