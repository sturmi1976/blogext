<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple TYPO3 Blog',
    'description' => 'With this record-based extension for TYPO3, it is easy to run a blog on your TYPO3 website, including RSS functionality and integration into the Google Sitemap with route enhancers for SEO-friendly links.',
    'category' => 'Frontend Plugins', 
    'version' => '1.3.0',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Andre Lanius',
    'author_email' => 'info@andre-lanius.site',
    'author_company' => 'Andre Lanius Web', 
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'php' => '8.2.0-8.4.99', 
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Lanius\\Blogext\\' => 'Classes/', 
        ],
    ],
];
