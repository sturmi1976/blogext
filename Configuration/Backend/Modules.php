<?php

declare(strict_types=1);

use Lanius\Blogext\Backend\Controller\BlogBackendController;


return [
    'blogcategory' => [
        'labels' => 'LLL:EXT:blogext/Resources/Private/Language/Module/locallang_modulcategory.xlf:mlang_tabs_tab',
        'position' => ['after' => 'web'],
        'iconIdentifier' => 'tx-blogext-module-head',
        //'navigationComponentId' => 'TYPO3.CMS.Backend.NavigationContainer', 
        'navigationComponentId' => '',
        'navigationFrameModule' => true,
    ],
    'blogext' => [
        'parent' => 'blogcategory',
        'position' => ['after' => 'web'],
        'access' => 'user',
        'workspaces' => 'live',
        'labels' => 'LLL:EXT:blogext/Resources/Private/Language/Module/locallang_mod.xlf:mod_title',
        'extensionName' => 'Blogext',
        'iconIdentifier' => 'tx-blogext-module', 
        'navigationFrameModule' => true,
        'module.register.pageRenderer.addCssFile' => [
            'EXT:blogext/Resources/Public/Css/Backend/backend.css' 
        ],
        'controllerActions' => [
            BlogBackendController::class => [
                'index', 'list', 'delete'
            ],
        ],
        'nonCacheableControllerActions' => [
                BlogBackendController::class => [
                    'delete', 'index', 'list'
                ],
    ],
    ],
   
];
