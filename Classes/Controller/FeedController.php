<?php
namespace Lanius\Blogext\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;


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
        
        // Flexform datas
        $flexformData = $this->settings;

        $pid = $flexformData['dataPid'];

        // Lade Blog-Beiträge aus der Datenbank
        $blogEntries = $this->postRepository->findAllBlogArticle($pid);


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
        $flexformData = $this->settings;

        $rss = '<?xml version="1.0" encoding="UTF-8"?>';
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $rss .= '<channel>';
        $rss .= '<title>'.$flexformData['title'].'</title>';
        $rss .= '<link>' . GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . '</link>';
        $rss .= '<description>'.$flexformData['desc'].'</description>';
        $rss .= '<language>de-DE</language>';

        foreach ($blogEntries as $entry) {

            $uri = $this->uriBuilder
            ->setTargetPageUid($flexformData['pid'])
            ->setArguments(['tx_blogext_bloglist[controller]'=>'Blog', 'tx_blogext_bloglist[action]'=>'show', 'tx_blogext_bloglist[blogUid]'=>$entry['uid']])
            ->build();

            $publishDate = new \DateTime();
            $publishDate->setTimestamp($entry['tstamp']);

            $rss .= '<item>';
            $rss .= '<title>' . htmlspecialchars($entry['title']) . '</title>';
            $rss .= '<link>'.$flexformData['domain'].''.$uri.'</link>';
            $rss .= '<description>' .strip_tags($entry['teaser']). '</description>';
            $rss .= '<pubDate>' .htmlspecialchars($publishDate->format('r')). '</pubDate>';
            $rss .= '</item>';
        }

        $rss .= '</channel>';
        $rss .= '</rss>';

        return $rss; 
    }
}
