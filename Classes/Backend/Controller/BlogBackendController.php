<?php

declare(strict_types=1);

namespace Lanius\Blogext\Backend\Controller;


use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Frontend\Service\TypoLinkCodecService;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Page\PageRenderer;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;

use Lanius\Blogext\Domain\Repository\PostRepository;
use Lanius\Blogext\Domain\Repository\CategoryRepository;



final class BlogBackendController  extends ActionController
{


    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
    */
    protected $defaultStorage;
    

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * categoryRepository
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param PostRepository $postRepository
     */
    public function injectPostRepository(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    
    


    public function indexAction(): ResponseInterface
    {
        $get = $this->request->getArguments();

        if(isset($get['deleted'])) {
            $deleted=true;
        }else{
            $deleted=false;
        }

        
        if ($this->postRepository === null) {
            $this->postRepository = GeneralUtility::makeInstance(\Lanius\Blogext\Domain\Repository\PostRepository::class);
        }
        if ($this->categoryRepository === null) {
            $this->categoryRepository = GeneralUtility::makeInstance(\Lanius\Blogext\Domain\Repository\CategoryRepository::class);
        }

        /** @var IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $newIcon = $iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL)->render();
        $showIcon = $iconFactory->getIcon('actions-view', Icon::SIZE_SMALL)->render();
        $hideIcon = $iconFactory->getIcon('tx-blogext-eye-strike', Icon::SIZE_SMALL)->render();

        $blogCount = $this->postRepository->findBlogCount();


        $counts = $this->postRepository->findBlogCountBackend();
        $anzahl_datensaetze = $counts[0]['counts'];
 
        // Page ID
        if(isset($this->settings['pageid'])) {
            $pageid = (int)$this->settings['pageid'];
        }
        
        // Folder UID
        if(isset($this->settings['pid'])) {
            $pid = (int)$this->settings['pid'];
        }


        // Link um einen neuen Blogartikel zu erstellen
        $newLink = $this->generateNewRecordLink('tx_blogext_domain_model_post', $pid);

        // Blog Articles
        $blogs = $this->loadLastArticles($pid, $limit=20);
        $i = 0;
        $articles = [];
        $editLinks = [];
        foreach($blogs as $blog) {
            $uid = (int)$blog['uid'];
            $table = 'tx_blogext_domain_model_post';
            $editLinks[$i] = $this->generateEditLink($uid, $table);
            $articles[$i] = $blog;
            $articles[$i]['url'][] = $editLinks[$i];
            $i++;
        }
        

        if(empty($this->settings['pageid']) || empty($this->settings['pid'])) {
            $this->view->assign('error', 'Bitte wähle in den Settings die PID und die Page ID.');
        }

        $this->view->assign('action', $get['action']);
        $this->view->assign('blogs', $articles);
        $this->view->assign('pageId', $pageid);
        $this->view->assign('edit', $editLinks);
        $this->view->assign('new', $newLink);
        $this->view->assign('newIcon', $newIcon);
        $this->view->assign('showIcon', $showIcon);
        $this->view->assign('hideIcon', $hideIcon);
        $this->view->assign('counts', $anzahl_datensaetze);
        $this->view->assign('deleted', $deleted);
        
        return $this->htmlResponse($this->view->render());

    }
   

    public function listAction(): ResponseInterface
    {
        $get = $this->request->getArguments();

        $this->view->assign('action', $get['action']);

        return $this->htmlResponse($this->view->render()); 

    }



    public function deleteAction(): ResponseInterface
    {
        $get = $this->request->getArguments();

        $uid = $get['uid'];

        $this->postRepository->markAsDeleted($uid);

        $uri = $this->uriBuilder->uriFor('index', ['deleted' => 1]);
        return $this->responseFactory->createResponse(307)
            ->withHeader('Location', $uri);

        $this->view->assign('getParams', $get);
        $this->view->assign('action', $get['action']);

        return $this->htmlResponse($this->view->render()); 

    }




    public function generateEditLink(int $recordUid, string $tableName): string
    {
        // Erzeuge einen Link zur Bearbeitungsmaske
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $editParameters = [
            'edit' => [
                $tableName => [
                    $recordUid => 'edit',
                ],
            ],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'), // Zurück zum Modul
        ];

        return (string)$uriBuilder->buildUriFromRoute('record_edit', $editParameters);
    }


    public function generateNewRecordLink(string $tableName, $pid): string
{
    // Erzeuge einen Link zur Erstellung eines neuen Datensatzes
    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    $newParameters = [
        'edit' => [
            $tableName => [
                $pid => 'new',
            ],
        ],
        'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'), // Zurück zum Modul
    ];

    return (string)$uriBuilder->buildUriFromRoute('record_edit', $newParameters);
}



    public function loadLastArticles($pid, $limit) {
        // Databases init
        if ($this->postRepository === null) {
            $this->postRepository = GeneralUtility::makeInstance(\Lanius\Blogext\Domain\Repository\PostRepository::class);
        }
        if ($this->categoryRepository === null) {
            $this->categoryRepository = GeneralUtility::makeInstance(\Lanius\Blogext\Domain\Repository\CategoryRepository::class);
        }

        //$blogs = $this->postRepository->findAllBlogArticle($pid);
        $blogs = $this->postRepository->findLatestEntries($pid, $limit);

        return $blogs;
    }

}
