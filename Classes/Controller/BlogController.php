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

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;


use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;



class BlogController extends ActionController
{
 
    private const SESSION_KEY = 'captcha_text';
    

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
    */
    protected $defaultStorage;


    

    /**
     * postRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\PostRepository
     */
    protected $postRepository = null;

    /**
     * categoryRepository
     *
     * @var \Lanius\Blogext\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository = null;



    /**
     * @param \Lanius\Blogext\Domain\Repository\PostRepository $postRepository
     */
    public function injectPostRepository(\Lanius\Blogext\Domain\Repository\PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }


    /**
     * @param \Lanius\Blogext\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(\Lanius\Blogext\Domain\Repository\CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


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
     * action list
     *
     * @param \Lanius\Blogext\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tagAction(): ResponseInterface
    {

        $get = $this->request->getArguments();
        $tagUid = $get['tagUid'];

        // Flexform datas
        $flexformData = $this->settings;

        if(isset($get['seite'])) {
            $seite = $get['seite']*$flexformData['perPage']-$flexformData['perPage'];
        }else{
            $seite = 1*$flexformData['perPage']-$flexformData['perPage']; 
        }

        // Anzahl Datensätze
        $counts = $this->tagRepository->findBlogCount($tagUid);
        $anzahl_datensaetze = $counts[0]['counts'];


        $SitesComplete = ceil($anzahl_datensaetze / $flexformData['perPage']);


        $blogs = $this->tagRepository->findTagsByUid($tagUid, $seite, $flexformData['perPage']);

        $tag = $this->tagRepository->findTag($tagUid);

        // Title Tag
        $titleProvider = GeneralUtility::makeInstance(TitleProvider::class);
        $titleProvider->setTitle('Tag: '.$tag[0]['title']);

        $articles = [];
        $i=0;
        foreach($blogs as $blog) {

            // Author load
            $author = $this->postRepository->findAuthorByUid($blog['author']);

            // Kategorien laden
            $categoriesIds = $blog['categories'];
            $categoryArray = explode(",",$categoriesIds);

            // Kategorien auslesen
            $cat = [];

            $catloop=0;

            foreach($categoryArray as $category) {
                $cat[] = $this->postRepository->findCategoriesByBlogUid($category); 
                $catloop++;
            }

            $articles[$i]['blog'] = $blog;
            $articles[$i]['blog']['author'] = $author[0];
            $articles[$i]['blog']['categories'] = $cat;

            $i++;
        }


        /* Paging nur anzeigen, wenn mehr als 1 Seite vorhanden ist ... */
        if($SitesComplete > 1) {
            if(isset($get['seite'])) {
                $seite = $get['seite'];
            }else{
                $seite=1;
            }
            $this->view->assign('paging', $this->blaetterfunktion3($seite, $SitesComplete));
        }



        $this->view->assign('blogs', $articles);
        $this->view->assign('count', count($articles));

        return $this->htmlResponse();  
    }



    /**
     * action list
     *
     * @param \Lanius\Blogext\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $context = GeneralUtility::makeInstance(Context::class);

        // Flexform datas
        $flexformData = $this->settings;

        // All arguments
        $get = $this->request->getArguments();


        if(isset($get['seite'])) {
            $seite = $get['seite']*$flexformData['perPage']-$flexformData['perPage'];
        }else{
            $seite = 1*$flexformData['perPage']-$flexformData['perPage']; 
        }

        // Anzahl Datensätze
        $counts = $this->postRepository->findBlogCount();
        $anzahl_datensaetze = $counts[0]['counts'];

        //$SitesComplete = ceil($anzahl_datensaetze / $this->settings['pager']['maxItemsPerPageUserlist']);
        $SitesComplete = ceil($anzahl_datensaetze / $flexformData['perPage']);

        $get_all_blogs = $this->postRepository->findBlogAllList($seite, $flexformData['perPage']);

        // Set a new title tag
        if(isset($get['seite'])) {
            $p = ' - Seite '.$get['seite'];
        }else {
            $p = ' - Seite 1';
        }
        $titleProvider = GeneralUtility::makeInstance(TitleProvider::class);
        $titleProvider->setTitle($flexformData['seo_title'].$p);

        $articles = [];

        $blogs = $this->postRepository->findBlogAllList($seite, $flexformData['perPage']);

        // Meta Tag Description
        if(isset($flexformData['seo_description'])) {
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('description');
            $metaTagManager->addProperty('description', $flexformData['seo_description']);
        }


        $i=0;
        $u=0;
        foreach($blogs as $blog) {
            // Content Elemente laden aus tt_content
            $tt_content = $this->postRepository->findAllWithContentElements($blog['uid'], $flexformData['dataPid']);

            // Author load
            $author = $this->postRepository->findAuthorByUid($blog['author']);

            // Kategorien laden
            $categoriesIds = $blog['categories'];
            $categoryArray = explode(",",$categoriesIds);

            // Kategorien auslesen
            $cat = [];

            $catloop=0;

            foreach($categoryArray as $category) {
                $cat[] = $this->postRepository->findCategoriesByBlogUid($category); 
                $catloop++;
            }

            $articles[$i]['blog'] = $blog;
            $articles[$i]['blog']['author'] = $author[0];
            $articles[$i]['blog']['categories'] = $cat;
            $articles[$i]['blog']['contentElements'] = $tt_content;

            
            // tt_content Inhalte laden
            foreach($tt_content as $content) {

                $articles[$i]['blog']['tt_content'][$u] = $this->renderContentElement($content['uid']);

                $u++;
            }

            $i++;
        }


        
        /* Paging nur anzeigen, wenn mehr als 1 Seite vorhanden ist ... */
        if($SitesComplete > 1) {
            if(isset($get['seite'])) {
                $seite = $get['seite'];
            }else{
                $seite=1;
            }
            $this->view->assign('paging', $this->blaetterfunktion($seite, $SitesComplete));
        }
        

       $this->view->assign('blogs', $articles);




        return $this->htmlResponse();  

    }


    /**
     * action show
     *
     * @param \Lanius\Blogext\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(): ResponseInterface
    {

        $context = GeneralUtility::makeInstance(Context::class);
        // Abruf der aktuellen sys_language_uid
        $currentLanguageUid = $context->getPropertyFromAspect('language', 'id');


        //$referer = $_SERVER["HTTP_REFERER"];
        // Aktuelle Domain abrufen
        /*
        $currentDomain = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        if(strpos($referer, $currentDomain) !== false) {
            $set_backlink = 1;
        }else{
            $set_backlink = ''; 
        }
        */

        // Flexform datas
        $flexformData = $this->settings;

        /* All get params to the forum showAction */
        $get = $this->request->getArguments();

        if(isset($get['success'])) {
            $success = 1;
        }else {
            $success = 0;
        }

        if(isset($get['captcha_false'])) {
            $captcha_false = 1;
        }else {
            $captcha_false = 0;
        }


        $blog = $this->postRepository->findBlogByUid($get['blogUid']);

        // Content Elemente laden aus tt_content
        $tt_content = $this->postRepository->findAllWithContentElements($get['blogUid'], $flexformData['dataPid']);

        // Author
        $author = $this->postRepository->findAuthorByUid($blog[0]['author']);


        $categoriesIds = $blog[0]['categories'];
        $categoryArray = explode(",",$categoriesIds);

        // Kategorien auslesen
        $cat = [];
        $i=0;

        foreach($categoryArray as $category) {
            $cat[] = $this->postRepository->findCategoriesByBlogUid($category);
            $i++;
        }


        // Set a new title tag
        if(isset($blog[0]['seo_title'])) {
            $title = htmlspecialchars($blog[0]['seo_title']);
        }else{
            $title = htmlspecialchars($blog[0]['title']);
        }
        $titleProvider = GeneralUtility::makeInstance(TitleProvider::class);
        $titleProvider->setTitle($title.' / '.$cat[0][0]['title']);

        // Meta Tags
        if(isset($blog[0]['description'])) {
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('description');
            $metaTagManager->addProperty('description', $blog[0]['description']);
        }
        if(isset($blog[0]['meta_keywords'])) {
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('keywords');
            $metaTagManager->addProperty('keywords', $blog[0]['meta_keywords']);
        }


        $tt_content = $this->postRepository->findAllWithContentElements($blog[0]['uid'], $flexformData['dataPid']);


        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        
        if (!isset($GLOBALS['TSFE'])) {
            $GLOBALS['TSFE'] = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], 1, 0);
            $GLOBALS['TSFE']->connectToDB();
            $GLOBALS['TSFE']->initFEuser();
            $GLOBALS['TSFE']->fetch_the_id();
            $GLOBALS['TSFE']->getPageAndRootline();
            $GLOBALS['TSFE']->initTemplate();
            $GLOBALS['TSFE']->getConfigArray();
        }

        foreach ($tt_content as &$ausgabe) {
            $ausgabe['renderedContent'] = $contentObjectRenderer->cObjGetSingle(
                'RECORDS',
                [
                    'source' => $ausgabe['uid'], // UID des Datensatzes
                    'tables' => 'tt_content',
                ]
            );
        }

        // Captcha generieren
        $captchaImage = $this->generateCaptcha();


        // Daten an das Fluid-Template übergeben
        $this->view->assign('captchaImage', $captchaImage);

        // Comments counts
        $count = $this->postRepository->findCommentsCount($get['blogUid']);

        // Comments load
        $comments = $this->postRepository->findCommentsByBloguid($get['blogUid']);
        

        $this->view->assign('blog', [
            'blog' => $blog,
            'author' => $author,
            'categories' => $cat,
            'tt_content' => $tt_content,
            'count' => $count,
            'success' => $success,
            'captcha_false' => $captcha_false,
            'comments' => $comments
        ]);


        if(isset($get['captcha_false'])) {
            $this->view->assign('name', $get['name']);
            $this->view->assign('email', $get['email']);
            $this->view->assign('url', $get['url']);
            $this->view->assign('comment', $get['comment']);
        }

        return $this->htmlResponse(); 


    }



    private function generateCaptcha(): string
    {
        // Captcha-Code generieren
        $captchaText = $this->generateCaptchaText();
        // Captcha in der Session speichern
        $this->storeCaptchaInSession($captchaText);

        // Captcha-Bild erstellen
        return $this->createCaptchaImage($captchaText);
    }

    private function generateCaptchaText(int $length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }

    private function createCaptchaImage(string $text): string
    {
        $width = 200;
        $height = 70;
        $image = imagecreate($width, $height);

        // Farben definieren
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $lineColor = imagecolorallocate($image, 64, 64, 64);

        // Störungen hinzufügen
        for ($i = 0; $i < 10; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // Text auf das Bild setzen
        $font = __DIR__ . '/../../Resources/Private/Fonts/OpenSans-Bold.ttf'; // Pfad zur Schriftart
        $fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('blogext') . 'Resources/Public/Fonts/OpenSans-Bold.ttf';
        $fontSize = 24;
        if (file_exists($fontPath)) {
            imagettftext($image, 28, rand(-10, 10), 40, 50, $textColor, $fontPath, $text);
        } else {
            imagestring($image, 5, 50, 25, $text, $textColor);
        }

        // Bild als Base64-String kodieren
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    public function storeCaptchaInSession(string $captchaText): void
    {
        session_start();
        $_SESSION['captcha_text'] = $captchaText;
    }

    public function getCaptchaFromSession(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['captcha_text'] ?? null;
    }

    public function clearCaptchaFromSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['captcha_text']);
    }


    public function verifyCaptcha()
    {
        // Benutzerinput abrufen
        $userInput = $this->request->getArguments('captcha_input');
        
        // Captcha-Text aus der Session abrufen
        $storedCaptcha = $this->getCaptchaFromSession();

        // Validierung
        if ($userInput['captcha_input'] == $storedCaptcha) {
            //$this->addFlashMessage('Captcha korrekt!');
            return true;
        } else {
            //$this->addFlashMessage('Captcha falsch. Bitte versuchen Sie es erneut.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            return false;
        }

        // Captcha nach Validierung löschen
        $this->clearCaptchaFromSession();
    }


    public function writeCommentAction(): ResponseInterface
    {
        /* All get params to the forum showAction */
        $get = $this->request->getArguments();

        /* Flexform */
        $flexformData = $this->settings;

        /* Data from form */
        if(isset($get['submit'])) {
            $name       = htmlspecialchars($get['name']);
            $email      = htmlspecialchars($get['email']);
            $website    = htmlspecialchars($get['url']); 
            $message    = htmlspecialchars($get['comment']);
            $time       = time();
            $pid        = $flexformData['dataPid'];
            $uid        = $get['uid'];
            $hidden     = $flexformData['comments'];
            
            $data = [
                'pid' => $pid,
                'bloguid' => $uid,
                'name' => $name,
                'email' => $email,
                'url' => $website,
                'comment' => $message,
                'time' => $time,
                'hidden' => $hidden
            ];

            // Captcha prüfen
            if($this->verifyCaptcha() == true) {

                $insert = $this->postRepository->newCommentWrite($data);
                // Redirect to article
                $uri = $this->uriBuilder->uriFor('show', ['blogUid' => $get['uid'], 'success' => 1]);
                $uriWithHash = $uri . '#comments';
                return $this->responseFactory->createResponse(307)->withHeader('Location', $uriWithHash);
            }else{
                $uri = $this->uriBuilder->uriFor('show', ['blogUid' => $get['uid'], 'captcha_false' => 1, 'name' => $name, 'email' => $email, 'url' => $website, 'comment' => $message]);
                $uriWithHash = $uri . '#comments';
                return $this->responseFactory->createResponse(307)->withHeader('Location', $uriWithHash);
            }
            
        }

        return $this->htmlResponse(); 
    }



    /**
     * category show
     *
     * @param \Lanius\Blogext\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function categoryAction(): ResponseInterface
    {
        $get = $this->request->getArguments();

        // Meta Tag noindex
        //$metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('robots');
        //$metaTagManager->addProperty('robots', 'noindex, nofollow');

        $flexformData = $this->settings;
        $pid = $flexformData['dataPid'];

        // Category ID
        $categoryUid = $get['categoryUid'];

        if(isset($get['seite'])) {
            $seite = $get['seite']*$flexformData['perPage']-$flexformData['perPage'];
        }else{
            $seite = 1*$flexformData['perPage']-$flexformData['perPage']; 
        }

        // Anzahl Datensätze
        $counts = $this->categoryRepository->findBlogCount($categoryUid);
        $anzahl_datensaetze = $counts[0]['counts'];

        $SitesComplete = ceil($anzahl_datensaetze / $flexformData['perPage']);


        $categories_blogs = $this->categoryRepository->findAllCategoryArticle($pid, $categoryUid, $seite, $flexformData['perPage']);

        $categoryData = $this->categoryRepository->findCategoryByUid($categoryUid);

        // Set a new title tag
        if(isset($get['seite'])) {
            $p = ' - Seite '.$get['seite'];
        }else {
            $p = ' - Seite 1';
        }
        $titleProvider = GeneralUtility::makeInstance(TitleProvider::class);
        $titleProvider->setTitle($flexformData['seo_title'].$p);

        $articles = [];

        $blogs = $this->categoryRepository->findAllCategoryArticle($pid, $categoryUid, $seite, $flexformData['perPage']);


        $i=0;
        $u=0;
        foreach($blogs as $blog) {
            // Content Elemente laden aus tt_content
            $tt_content = $this->postRepository->findAllWithContentElements($blog['uid'], $flexformData['dataPid']);

            // Author load
            $author = $this->postRepository->findAuthorByUid($blog['author']);

            // Kategorien laden
            $categoriesIds = $blog['categories'];
            $categoryArray = explode(",",$categoriesIds);

            // Kategorien auslesen
            $cat = [];

            $catloop=0;

            foreach($categoryArray as $category) {
                $cat[] = $this->postRepository->findCategoriesByBlogUid($category); 
                $catloop++;
            }

            $articles[$i]['blog'] = $blog;
            $articles[$i]['blog']['author'] = $author[0];
            $articles[$i]['blog']['categories'] = $cat;
            $articles[$i]['blog']['contentElements'] = $tt_content;

            
            // tt_content Inhalte laden
            foreach($tt_content as $content) {

                $articles[$i]['blog']['tt_content'][$u] = $this->renderContentElement($content['uid']);

                $u++;
            }

            $i++;
        }


        /* Paging nur anzeigen, wenn mehr als 1 Seite vorhanden ist ... */
        if($SitesComplete > 1) {
            if(isset($get['seite'])) {
                $seite = $get['seite'];
            }else{
                $seite=1;
            }
            $this->view->assign('paging', $this->blaetterfunktion2($seite, $SitesComplete));
        }

        if($counts[0]['counts'] == 0) {
            $this->view->assign('not_found', '1');
        }

        $this->view->assign('blogs', $articles);
        $this->view->assign('category', $categoryData[0]);


        return $this->htmlResponse(); 
    }





    public function renderContentElement($contentElementUid)
{
    // Initialisierung des TypoScriptFrontendControllers (falls nicht bereits vorhanden)
    if (!isset($GLOBALS['TSFE']) || !is_object($GLOBALS['TSFE'])) {
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            1, // Ersetze durch die gewünschte Seiten-ID
            0  // TypeNum
        );

        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->getPageAndRootline();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    // Erstellen eines ContentObjectRenderer-Objekts
    $contentObjectRenderer = $GLOBALS['TSFE']->cObj;

    // Nutzung von cObjGetSingle mit RECORDS
    $renderedContent = $contentObjectRenderer->cObjGetSingle(
        'RECORDS',
        [
            'tables' => 'tt_content',
            'source' => $contentElementUid,
            'dontCheckPid' => 1 // Setze auf 0, wenn die PID-Überprüfung berücksichtigt werden soll
        ]
    );

    return $renderedContent;
}





public function blaetterfunktion($seite,$maxseite,$url="",$anzahl=4,$get_name="seite")
    {
        $return = "<ul class='pagination'>";

        /* All get params to the forum showAction */
        $get = $this->request->getArguments();
        
        $anhang = "?";
        if(substr($url,-1,1) == "&") {
            $url = substr_replace($url,"",-1,1);
        }
        else if(substr($url,-1,1) == "?") {
            $anhang = "?";
            $url = substr_replace($url,"",-1,1);
        }
        
        if($anzahl%2 != 0) $anzahl++; //Wenn $anzahl ungerade, dann $anzahl++
        
        $a = $seite-($anzahl/2);
        $b = 0;
        $blaetter = array();
        while($b <= $anzahl)
        {
            if($a > 0 AND $a <= $maxseite)
            {
                $blaetter[] = $a;
                $b++;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)>=0)
            {
                $blaetter = array();
                $a -= ($anzahl+2);
                $b = 0;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)<0)
            {
                break;
            }
            
            $a++;
        }
        $return .= "";
        if(!in_array(1,$blaetter) AND count($blaetter) > 1)
        {
            
            
            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments(['tx_blogext_bloglist[seite]'=>1])
            ->build();

            
            if(!in_array(2,$blaetter)) $return .= "<li><a href=\"".$uri."\">1</a></li><li><b class='more'>...</b></li>";
            else $return .= "<li><a href=\"".$uri."\">1</a></li>";
        }
        
        foreach($blaetter AS $blatt)
        {
            
            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments(['tx_blogext_bloglist[seite]'=>$blatt])
            ->build();
            
            
            if($blatt == $seite) $return .= "<li><b>$blatt</b></li>";
            else $return .= "<li><a href=\"".$uri."\">$blatt</a></li>";
        }
        
        if(!in_array($maxseite,$blaetter) AND count($blaetter) > 1)
        {
            
            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments(['tx_blogext_bloglist[seite]'=>$maxseite])
            ->build();
            
            if(!in_array(($maxseite-1),$blaetter)) $return .= "<li><b class='more'>...</b></li><li><a href=\"".$uri."\">letzte</a></li>";
            else $return .= "<li><a href=\"".$uri."\">$maxseite</a></li>";
        }
        
        $return .= '</ul>';

        if(empty($return)) {
            return  "&nbsp;<b>1</b>&nbsp;";
        }else {
            return $return; 
        }
    }






    public function blaetterfunktion2($seite,$maxseite,$url="",$anzahl=4,$get_name="seite")
    {
        $return = "<ul class='pagination'>"; 

        /* All get params to the forum showAction */
        $get = $this->request->getArguments();

        $categoryUid = $get['categoryUid'];
        
        $anhang = "?";
        if(substr($url,-1,1) == "&") {
            $url = substr_replace($url,"",-1,1);
        }
        else if(substr($url,-1,1) == "?") {
            $anhang = "?";
            $url = substr_replace($url,"",-1,1);
        }
        
        if($anzahl%2 != 0) $anzahl++; //Wenn $anzahl ungerade, dann $anzahl++
        
        $a = $seite-($anzahl/2);
        $b = 0;
        $blaetter = array();
        while($b <= $anzahl)
        {
            if($a > 0 AND $a <= $maxseite)
            {
                $blaetter[] = $a;
                $b++;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)>=0)
            {
                $blaetter = array();
                $a -= ($anzahl+2);
                $b = 0;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)<0)
            {
                break;
            }
            
            $a++;
        }
        $return .= "";
        if(!in_array(1,$blaetter) AND count($blaetter) > 1)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'category',
                    'controller' => 'Blog',
                    'categoryUid' => $categoryUid,
                    'seite' => 1
                ]
            ])
            ->build();
            
            if(!in_array(2,$blaetter)) $return .= "<li><a href=\"".$uri."\">1</a></li><li><b class='more'>...</b></li>";
            else $return .= "<li><a href=\"".$uri."\">1</a></li>";
        }
        
        foreach($blaetter AS $blatt)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'category',
                    'controller' => 'Blog',
                    'categoryUid' => $categoryUid,
                    'seite' => $blatt
                ]
            ])
            ->build();
            
            if($blatt == $seite) $return .= "<li><b>$blatt</b></li>";
            else $return .= "<li><a href=\"".$uri."\">$blatt</a></li>";
        }
        
        if(!in_array($maxseite,$blaetter) AND count($blaetter) > 1)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'category',
                    'controller' => 'Blog',
                    'categoryUid' => $categoryUid,
                    'seite' => $maxseite
                ]
            ])
            ->build();
            
            if(!in_array(($maxseite-1),$blaetter)) $return .= "<li><b class='more'>...</b></li><li><a href=\"".$uri."\">letzte</a></li>";
            else $return .= "<li><a href=\"".$uri."\">$maxseite</a></li>";
        }
        
        $return .= '</ul>';

        if(empty($return)) {
            return  "&nbsp;<b>1</b>&nbsp;";
        }else {
            return $return; 
        }
    }






    public function blaetterfunktion3($seite,$maxseite,$url="",$anzahl=4,$get_name="seite")
    {
        $return = "<ul class='pagination'>"; 

        /* All get params to the forum showAction */
        $get = $this->request->getArguments();

        $tagUid = $get['tagUid'];
        
        $anhang = "?";
        if(substr($url,-1,1) == "&") {
            $url = substr_replace($url,"",-1,1);
        }
        else if(substr($url,-1,1) == "?") {
            $anhang = "?";
            $url = substr_replace($url,"",-1,1);
        }
        
        if($anzahl%2 != 0) $anzahl++; //Wenn $anzahl ungerade, dann $anzahl++
        
        $a = $seite-($anzahl/2);
        $b = 0;
        $blaetter = array();
        while($b <= $anzahl)
        {
            if($a > 0 AND $a <= $maxseite)
            {
                $blaetter[] = $a;
                $b++;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)>=0)
            {
                $blaetter = array();
                $a -= ($anzahl+2);
                $b = 0;
            }
            else if($a > $maxseite AND ($a-$anzahl-2)<0)
            {
                break;
            }
            
            $a++;
        }
        $return .= "";
        if(!in_array(1,$blaetter) AND count($blaetter) > 1)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'tag',
                    'controller' => 'Blog',
                    'tagUid' => $tagUid,
                    'seite' => 1
                ]
            ])
            ->build();
            
            if(!in_array(2,$blaetter)) $return .= "<li><a href=\"".$uri."\">1</a></li><li><b class='more'>...</b></li>";
            else $return .= "<li><a href=\"".$uri."\">1</a></li>";
        }
        
        foreach($blaetter AS $blatt)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'tag',
                    'controller' => 'Blog',
                    'tagUid' => $tagUid,
                    'seite' => $blatt
                ]
            ])
            ->build();
            
            if($blatt == $seite) $return .= "<li><b>$blatt</b></li>";
            else $return .= "<li><a href=\"".$uri."\">$blatt</a></li>";
        }
        
        if(!in_array($maxseite,$blaetter) AND count($blaetter) > 1)
        {

            $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments([
                'tx_blogext_bloglist' => [
                    'action' => 'tag',
                    'controller' => 'Blog',
                    'tagUid' => $tagUid,
                    'seite' => $maxseite
                ]
            ])
            ->build();
            
            if(!in_array(($maxseite-1),$blaetter)) $return .= "<li><b class='more'>...</b></li><li><a href=\"".$uri."\">letzte</a></li>";
            else $return .= "<li><a href=\"".$uri."\">$maxseite</a></li>";
        }
        
        $return .= '</ul>';

        if(empty($return)) {
            return  "&nbsp;<b>1</b>&nbsp;";
        }else {
            return $return; 
        }
    }



}
