<?php
namespace Lanius\Blogext\Domain\Repository;

use \TYPO3\CMS\Extbase\Persistence\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection; 
use TYPO3\CMS\Core\Utility\GeneralUtility;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;




class PostRepository extends Repository
{

    public function findBlogByUid(int $uid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_post WHERE uid="'.$uid.'" AND hidden="0" AND deleted="0" LIMIT 1');
        $result = $query->execute(true);
        return $result;

    }

    public function findCategoriesByBlogUid(int $uid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_category WHERE uid="'.$uid.'" AND hidden="0" AND deleted="0" ORDER BY title');
        $result = $query->execute(true);
        return $result;
    }

    public function findAllBlogArticle(int $pid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_post WHERE pid="'.$pid.'" AND hidden="0" AND deleted="0" AND sys_language_uid="'.$GLOBALS['TSFE']->sys_language_uid.'" ORDER BY crdate DESC');
        $result = $query->execute(true);
        return $result;
    }


    public function findAllWithContentElements(int $parentId, int $pid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tt_content WHERE tx_blogext_parent="'.$parentId.'" AND pid="'.$pid.'" AND hidden="0" AND deleted="0" ORDER BY sorting');
        $result = $query->execute(true);
        return $result;
    }


    public function findAuthorByUid(int $uid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM be_users WHERE uid="'.$uid.'" LIMIT 1');
        $result = $query->execute(true);
        return $result;
    }

    public function findBlogCount() {
        $query = $this->createQuery(); 
        $query->statement('SELECT COUNT(uid) AS counts FROM tx_blogext_domain_model_post WHERE deleted=0 AND hidden=0');
        $result = $query->execute(true);
        return $result;
    }

    public function findBlogAllList($page,$maxItemsPerPageBlogList) {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_post WHERE hidden="0" AND deleted="0" ORDER BY crdate DESC LIMIT '.$page.','.$maxItemsPerPageBlogList.'');
        $result = $query->execute(true); 
        return $result;
    }


}
