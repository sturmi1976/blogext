<?php

declare(strict_types=1);

use Lanius\Blogext\Backend\Controller\BlogBackendController;


return [
    'blogcategory' => [
        'labels' => 'LLL:EXT:blogext/Resources/Private/Language/Module/locallang_modulcategory.xlf',
        'position' => ['after' => 'web'],
        'iconIdentifier' => 'content-text',
        'navigationComponentId' => '', // Optional, wenn eine Navigation benötigt wird
    ],
    't3blog' => [
        'parent' => 'web',
        'position' => ['after' => 'web'],
        'access' => 'user',
        'workspaces' => 'live',
        'labels' => 'LLL:EXT:blogext/Resources/Private/Language/Module/locallang_mod.xlf',
        'extensionName' => 'Blogext',
        'iconIdentifier' => 'content-text',
        'controllerActions' => [
            BlogBackendController::class => [
                'index',
            ],
        ],
    ],
   
];
