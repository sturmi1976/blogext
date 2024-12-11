<?php
return [
    'ctrl' => [
        'title' => 'Blog Post / Beitrag',
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
        '1' => ['showitem' => 'title, tags, url_segment, teaser, author, categories, comments_disable, sorting, --div--; SEO, seo_title, description, meta_keywords, --div--;Inhalte, content_elements, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden'],
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
        'comments_disable' => [
            'exclude' => true,
            'label' => 'Kommentare deaktivieren',
            'config' => [
                'type' => 'check',
            ],
        ],
        'categories' => [
            'exclude' => 0,
            'label' => 'Kategorien',
            'config' => [
                'type' => 'select', 
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_blogext_domain_model_category',
                'size' => 10,
                'maxitems' => 9999,
                'multiple' => 0,
                'enableMultiSelectFilterTextfield' => true,
                'fieldControl' => [
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'label' => 'Teaser für die Listenansicht',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 15,
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
        'description' => [
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
        'author' => [
            'exclude' => 1,
            'label' => 'Author / Verfasser',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'items' => [
                    ['-- Select Author --', 0]
                ],
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
                'size' => 1,
            ],
        ],
        'tags' => [
        'exclude' => true,
        'label' => 'Tags',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'tx_blogext_domain_model_tags',
            'size' => 10,
        ],
    ],
        'content_elements' => [
            'exclude' => true,
            'label' => 'Content Elements',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
                'foreign_field' => 'tx_blogext_parent', // Ein benutzerdefiniertes Feld
                'appearance' => [
                    'collapseAll' => true,
                    'newRecordLinkPosition' => 'bottom',
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => true,
                    'showAllLocalizationLink' => true,
                    'useSortable' => true, // optional für Sortierbarkeit
                    'enabledControls' => [
                        'info' => true,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => true,
                        'hide' => true,
                        'delete' => true,
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
