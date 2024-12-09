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
    public function newCommentWrite($insert_array) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_blogext_domain_model_comments');
        $affectedRows = $queryBuilder
        ->insert('tx_blogext_domain_model_comments')
        ->values([
            'pid' => $insert_array['pid'],
            'tstamp' => $insert_array['time'],
            'crdate' => $insert_array['time'],
            'name' => $insert_array['name'],
            'email' => $insert_array['email'],
            'url' => $insert_array['url'],
            'comment' => $insert_array['comment'],
            'bloguid' => $insert_array['bloguid'],
            'hidden' => $insert_array['hidden']
        ])
        ->executeStatement();
        
        $Uid = $queryBuilder->getConnection()->lastInsertId();
        
        return $Uid;
}

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
        $query->statement('SELECT * FROM tx_blogext_domain_model_post WHERE pid="'.$pid.'" AND hidden="0" AND deleted="0" ORDER BY crdate DESC');
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

    public function findCommentsCount($blogUid) {
        $query = $this->createQuery();
        $query->statement('SELECT COUNT(uid) AS count FROM tx_blogext_domain_model_comments WHERE bloguid="'.$blogUid.'" AND hidden="0" AND deleted="0"');
        $result = $query->execute(true);
        return $result;  
    }

    public function findCommentsByBloguid($bloguid) {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_comments WHERE hidden="0" AND deleted="0" AND bloguid="'.$bloguid.'" ORDER BY crdate DESC');
        $result = $query->execute(true); 
        return $result; 
    }




    /* Datenbankabfragen die im Backend genutzt werden */

    /* Letzte 20 Blog Artikel für das Dashboard */
    public function findLatestEntries($pid, $limit = 20): array
    {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_blogext_domain_model_post');

        $queryBuilder->getRestrictions()->removeByType(\TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction::class);

        return $queryBuilder
            ->select('*') 
            ->from('tx_blogext_domain_model_post') 
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0)),
                $queryBuilder->expr()->in('hidden', [0, 1])
            )
            ->orderBy('crdate', 'DESC') 
            ->setMaxResults($limit) 
            ->executeQuery()
            ->fetchAllAssociative(); 
    }



    /* Artikel als gelöscht markieren */
    public function markAsDeleted($mainRecordUid): void
    {
        $queryBuilderMain = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_blogext_domain_model_post');

        // Hauptdatensatz als gelöscht markieren
        $queryBuilderMain
            ->update('tx_blogext_domain_model_post')
            ->where(
                $queryBuilderMain->expr()->eq('uid', $queryBuilderMain->createNamedParameter($mainRecordUid))
            )
            ->set('deleted', 1)
            ->executeStatement();

        // Verknüpfte tt_content-Datensätze als gelöscht markieren
        $queryBuilderContent = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilderContent
            ->update('tt_content')
            ->where(
                $queryBuilderContent->expr()->eq('tx_blogext_parent', $queryBuilderContent->createNamedParameter($mainRecordUid))
            )
            ->set('deleted', 1)
            ->executeStatement(); 
    }


    public function findBlogCountBackend() {
        $query = $this->createQuery(); 
        $query->statement('SELECT COUNT(uid) AS counts FROM tx_blogext_domain_model_post WHERE deleted=0');
        $result = $query->execute(true);
        return $result;
    }
 

}
