<?php

return [
    'ctrl' => [
        'title' => 'Tags',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',  
        'default_sortby' => 'ORDER BY sorting',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:blogext/Resources/Public/Icons/tx_yourextensionname_domain_model_yourtable.svg',
        'typeicon_classes' => [
            'default' => 'mimetypes-x-content-text',
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'title, url_segment, sorting, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'exclude' => true,
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required,uniqueInPid'
            ],
        ],
        'url_segment' => [
            'exclude' => true,
            'label' => 'URL / Slug',
            'config' => [
                'type' => 'slug',
                'size' => 30,
                'max' => 255,
                'eval' => 'uniqueInSite',
                'fallbackCharacter' => '-',
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
    ],
];
