<?php

declare(strict_types=1);

namespace Lanius\Blogext\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

final class TitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
