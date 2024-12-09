<?php

return [
    'ctrl' => [
        'title' => 'Kategorie',
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
        '1' => ['showitem' => 'title, url_segment, sorting, --div--; SEO, seo_title, meta_description, meta_keywords, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden'],
    ],
    'columns' => [
        'title' => [
            'exclude' => true,
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
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
        'seo_title' => [
            'exclude' => true,
            'label' => 'Meta Title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'meta_description' => [
            'exclude' => true,
            'label' => 'Meta Description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'meta_keywords' => [
            'exclude' => true,
            'label' => 'Meta Keywords',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
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
