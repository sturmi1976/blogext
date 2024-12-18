<?php

namespace Lanius\Blogext\Controller;


use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;





class LastController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
    */
    protected $defaultStorage;

    /**
     * tagRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\postRepository
     */
    protected $postRepository = null;

 
    /**
     * @param \Lanius\Blogext\Domain\Repository\postRepository $postRepository
     */
    public function injectPostRepository(\Lanius\Blogext\Domain\Repository\PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }


    /**
     * list show
     *
     * @param \Lanius\Blogext\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface 
    {
        // Flexform datas
        $flexformData = $this->settings;

        // Blog articles
        $blogs = $this->postRepository->getLastArticles($this->settings['count'], $this->settings['orderby']);

        
        // Wenn die Darstellung als jQuery Slider erfolgen soll
        if($flexformData['slider'] == 1) {
            if($flexformData['pager'] == 1) {
                $pager = true;
            }else{
                $pager = false;
            }
            
            if(empty($flexformData['slidetoscroll'])) {
                $slidetocroll = 1;
            }else{
                $slidetocroll = $flexformData['slidetoscroll'];
            }

            if(empty($flexformData['autoplay'])) {
                $autoplay = false;
            }else{
                $autoplay = true;
            }

            $this->view->assign('sliderArt', $this->settings['sliderart']);
            $this->view->assign('blogs', $blogs);
            $this->view->assign('sliderCountItems', (int)$flexformData['showitems']);
            $this->view->assign('cropping', intval($flexformData['cropping'])); 
            $this->view->assign('pager', $pager); 
            $this->view->assign('slidersettings', $flexformData);
            $this->view->assign('slidetoscrolling', (int)$slidetocroll);
            $this->view->assign('autoplay', $autoplay);
        }else{
            // Wenn die Darstellung nur statisch erfolgen soll (ohne Slide-Funktion) 
            $this->view->assign('slider', 0);
            $this->view->assign('blogs', $blogs);
        }

       

        return $this->htmlResponse(); 
    }



    public function slickSlider() {

        
    }


    public function bxSlider() {

        
    }


}