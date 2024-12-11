<?php
namespace Lanius\Blogext\Domain\Repository;

use \TYPO3\CMS\Extbase\Persistence\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection; 
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Doctrine\DBAL\ParameterType;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;




class TagRepository extends Repository
{

    public function findTags()
    {
        /*
        $sql = 'SELECT 
                t.uid, 
                t.title, 
                t.url_segment,
                COUNT(*) AS frequency 
            FROM 
                tx_blogext_domain_model_tags t
            JOIN 
                tx_blogext_domain_model_post p
            ON 
                FIND_IN_SET(t.uid, p.tags) > 0
            GROUP BY 
                t.uid, t.title
            ORDER BY 
                frequency DESC;';
        */
            $sql = 'SELECT 
            t.uid, 
            t.title, 
            t.url_segment,
            COUNT(*) AS frequency 
            FROM 
            tx_blogext_domain_model_tags t
            JOIN 
            tx_blogext_domain_model_post p
            ON 
            FIND_IN_SET(t.uid, p.tags) > 0
            GROUP BY 
            t.uid, t.title
            ORDER BY 
            RAND();';


        $query = $this->createQuery();
        $query->statement($sql);
        $result = $query->execute(true);
        return $result;

    }



    public function findTagsByUid($tagUid, $seite, $perPage) 
    {

        $tagUid = (int)$tagUid;

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable('tx_blogext_domain_model_post');

        /*
        $posts = $connection->executeQuery(
            'SELECT * FROM tx_blogext_domain_model_post WHERE hidden="0" AND deleted="0" FIND_IN_SET(?, tags) ORDER BY crdate DESC LIMIT '.$seite.','.$perPage.'',
            [$tagUid],
            [ParameterType::INTEGER]
        )->fetchAllAssociative();
        */
        $posts = $connection->executeQuery(
            'SELECT * 
             FROM tx_blogext_domain_model_post 
             WHERE hidden = 0 
               AND deleted = 0 
               AND FIND_IN_SET(?, tags) 
             ORDER BY crdate DESC 
             LIMIT ?, ?',
            [$tagUid, $seite, $perPage], // Parameter binden
            [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::INTEGER] // Typen der Parameter
        )->fetchAllAssociative();

        return $posts;
    }


    public function findTag($tagUid) {
        $tagUid = (int)$tagUid;

        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_tags WHERE uid="'.$tagUid.'" LIMIT 1');
        $result = $query->execute(true); 
        return $result; 

    }


    public function findBlogCount($tagUid) {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable('tx_blogext_domain_model_post');

        $posts = $connection->executeQuery(
            'SELECT COUNT(uid) AS counts FROM tx_blogext_domain_model_post WHERE FIND_IN_SET(?, tags)',
            [$tagUid],
            [ParameterType::INTEGER]
        )->fetchAllAssociative();
        return $posts;
    }
 

}
