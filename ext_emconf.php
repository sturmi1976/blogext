<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Blog',
    'description' => 'Blog Extension für TYPO3',
    'version' => '1.0.0',
    'state' => 'stable',
    'author' => 'Andre Lanius',
    'author_email' => 'info@andre-lanius.site',
    'author_company' => 'Andre Lanius - TYPO3 Freelancer',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Lanius\\Blogext\\' => 'Classes/', 
        ],
    ],
];
