<?php
namespace Lanius\Blogext\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FeedController extends ActionController
{

    /**
     * postRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\PostRepository
     */
    protected $postRepository = null;

    /**
     * @param \Lanius\Blogext\Domain\Repository\PostRepository $postRepository
     */
    public function injectPostRepository(\Lanius\Blogext\Domain\Repository\PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }



    /**
     * Action to generate RSS feed
     */
    public function rssAction(): void
    {
        // Lade Blog-Beiträge aus der Datenbank
        $blogEntries = $this->postRepository->findAllBlogArticle(22);

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($blogEntries);

        // Erstelle RSS-Feed-Inhalt
        $rssFeed = $this->generateRss($blogEntries);

        // Setze Header und gebe Feed aus
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $rssFeed;
        exit;
    }

    /**
     * Generates RSS feed content
     */
    private function generateRss($blogEntries): string
    {
        $rss = '<?xml version="1.0" encoding="UTF-8"?>';
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $rss .= '<channel>';
        $rss .= '<title>Mein Blog</title>';
        $rss .= '<link>' . GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . '</link>';
        $rss .= '<description>Aktuelle Blog-Posts</description>';

        foreach ($blogEntries as $entry) {
            $publishDate = new \DateTime();
            $publishDate->setTimestamp($entry['tstamp']);

            $rss .= '<item>';
            $rss .= '<title>' . htmlspecialchars($entry['title']) . '</title>';
            $rss .= '<link>'.GeneralUtility::getIndpEnv('TYPO3_SITE_URL').''.$entry['url_segment'].'</link>';
            $rss .= '<description>' .strip_tags($entry['teaser']). '</description>';
            $rss .= '<pubDate>' .htmlspecialchars($publishDate->format('r')). '</pubDate>';
            $rss .= '</item>';
        }

        $rss .= '</channel>';
        $rss .= '</rss>';

        return $rss;
    }
}
