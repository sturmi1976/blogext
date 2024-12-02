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




class CategorymenuController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
    */
    protected $defaultStorage;

    /**
     * categoryRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository = null;


    /**
     * @param \Lanius\Blogext\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(\Lanius\Blogext\Domain\Repository\CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * list show
     *
     * @param \Lanius\Blogext\Domain\Model\Category $category
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface 
    {
        if(isset($_GET['tx_blogext_bloglist'])) {
            $get = $_GET['tx_blogext_bloglist'];
                $catUid = $get['categoryUid'];
            }
            else {
                $catUid = "";
            }

        // Page ID
        $pageid = $this->settings['pid'];

        $categories = $this->categoryRepository->getCategoryMenu();

        $this->view->assign('categories', [
            'data' => $categories,
        ]);
        $this->view->assign('pageid', $pageid);
        $this->view->assign('catUid', $catUid);

        return $this->htmlResponse(); 
    }

}