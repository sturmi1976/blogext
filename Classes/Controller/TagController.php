<?php

namespace Lanius\Blogext\Controller;

use Lanius\Blogext\PageTitle\TitleProvider;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Mvc\Request; 
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\CMS\Core\Session\SessionInterface;




class TagController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
    */
    protected $defaultStorage;

    /**
     * tagRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\tagRepository
     */
    protected $tagRepository = null;


    /**
     * @param \Lanius\Blogext\Domain\Repository\TagRepository $tagRepository
     */
    public function injectTagRepository(\Lanius\Blogext\Domain\Repository\TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }


    /**
     * list show
     *
     * @param \Lanius\Blogext\Domain\Model\Tag $tag
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface 
    {
        // Flexform datas
        $flexformData = $this->settings;


        $animated = $GLOBALS['TSFE']->register['animate_tagcloud'] = $flexformData['animate'];


        $tags = $this->tagRepository->findTags();


        $minFontSize = 12; // Minimum Schriftgröße
        $maxFontSize = 50; // Maximum Schriftgröße

        
        $maxFrequency = max(array_column($tags, 'frequency'));
        $minFrequency = min(array_column($tags, 'frequency'));

        $frequencies = array_column($tags, 'frequency');


        $maxFrequency = max($frequencies);
        $minFrequency = min($frequencies);

        $size = 0;
        $i=0; 
        $array = [];
        $content = '<div class="tagcloud">';
        foreach ($tags as $tag) {
            $array[$i]['tag'] = $tag;
            if ($tag['frequency'] > 0) {
                $size = $this->calculateSize($tag['frequency'], $minFrequency, $maxFrequency, $minFontSize, $maxFontSize); 
            }
            $frequency = $tag['frequency'];
            //$tag['font_size'] = $minFontSize + ($frequency - $minFrequency) / ($maxFrequency - $minFrequency) * ($maxFontSize - $minFontSize);
            if ($maxFrequency != $minFrequency) {
                $tag['font_size'] = $minFontSize + ($frequency - $minFrequency) / ($maxFrequency - $minFrequency) * ($maxFontSize - $minFontSize);
            } else {
                // Fallback: Setze eine Standardgröße oder handle den Fall anders
                $tag['font_size'] = $minFontSize; // z.B. die minimale Schriftgröße verwenden
            }

            $content .= '<a href="/tag/' . urlencode($tag['title']) . '" style="font-size: ' . round($size) . 'px;">' . htmlspecialchars($tag['title']) . '</a> ';

            $array[$i]['tag'][] = $tag['font_size'];
            $i++;
            
        }
        $content .= '</div>';

        //$this->view->assign('content', $content);
        $this->view->assign('tags', $array);
        $this->view->assign('animate', $flexformData['animate']);
        $this->view->assign('animated', $animated);
        $this->view->assign('textcolor', $this->settings['textcolor']);
        $this->view->assign('bordercolor', $this->settings['bordercolor']);

        return $this->htmlResponse(); 
    }





    function calculateSize($frequency, $minFrequency, $maxFrequency, $minFontSize = 12, $maxFontSize = 30)
    {
        // Vermeidung von Division durch Null, falls alle Frequenzen gleich sind
        if ($maxFrequency === $minFrequency) {
            return $minFontSize;
        }

        // Berechnung der Größe
        return $minFontSize + ($frequency - $minFrequency) / ($maxFrequency - $minFrequency) * ($maxFontSize - $minFontSize);
    }

}