<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple TYPO3 Blog',
    'description' => 'Simple TYPO3 blog extension.',
    'category' => 'Frontend Plugins', 
    'version' => '1.0.3',
    'state' => 'stable',
    'author' => 'Andre Lanius',
    'author_email' => 'info@andre-lanius.site',
    'author_company' => 'Andre Lanius Web',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'php' => '8.2.0-8.3.99', 
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Lanius\\Blogext\\' => 'Classes/', 
        ],
    ],
];
