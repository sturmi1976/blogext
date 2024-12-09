<?php

declare(strict_types=1);

namespace Lanius\Blogext\Domain\Model;


/**
 * This file is part of the "Forum" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Andre Lanius <a-lanius@web.de>, AL Webdesign
 */

/**
 * Post
 */
class Post extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

   
    
    /**
     * title
     *
     * @var \Lanius\Blogext\Domain\Model\Title
     */
    protected $title = null;
    
    
    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    
            
            
}