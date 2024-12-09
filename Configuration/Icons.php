<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;


return [
    'tx-blogext-module' => [
        'provider' => BitmapIconProvider::class, 
        'source' => 'EXT:blogext/Resources/Public/Icons/blog-module.png',
    ],
    'tx-blogext-module-head' => [
        'provider' => BitmapIconProvider::class, 
        'source' => 'EXT:blogext/Resources/Public/Icons/blog-solid.png',
    ],
    'tx-blogext-eye-strike' => [
        'provider' => SvgIconProvider::class, 
        'source' => 'EXT:blogext/Resources/Public/Icons/eye-strike.svg',
    ],
];

