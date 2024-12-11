<?php
namespace Lanius\Blogext\Domain\Repository;

use \TYPO3\CMS\Extbase\Persistence\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection; 
use TYPO3\CMS\Core\Utility\GeneralUtility;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;


class CategoryRepository extends Repository
{

    public function findAllCategoryArticle($pid, $category, $page, $maxItemsPerPageBlogList)
    {
        $query = $this->createQuery();

        if (is_array($category)) {
            $conditions = [];
            foreach ($category as $cat) {
                $conditions[] = 'FIND_IN_SET(?, categories)';
            }
            $categoryCondition = implode(' OR ', $conditions);
            $queryParams = array_merge([$pid], $category);
        } else {
            $categoryCondition = 'FIND_IN_SET(?, categories)';
            $queryParams = [$pid, $category];
        }
    
        // Erstelle das SQL-Statement
        $query->statement(
            'SELECT * 
             FROM tx_blogext_domain_model_post 
             WHERE pid = ? 
               AND hidden = "0" 
               AND deleted = "0" 
               AND (' . $categoryCondition . ') 
             ORDER BY crdate DESC 
             LIMIT ?, ?',
            array_merge($queryParams, [$page, $maxItemsPerPageBlogList])
        );
    
        // Führe die Abfrage aus
        $result = $query->execute(true);
        return $result;
    }


    public function findBlogCount($category) 
    {
        $query = $this->createQuery();
        if (is_array($category)) {
            // Mehrere Kategorien als Array -> Umwandeln in eine WHERE-Bedingung mit OR
            $conditions = [];
            foreach ($category as $cat) {
                $conditions[] = 'FIND_IN_SET(?, categories)';
            }
            $whereClause = implode(' OR ', $conditions);
            $queryParams = $category;
        } else {
            // Einzelne Kategorie
            $whereClause = 'FIND_IN_SET(?, categories)';
            $queryParams = [$category];
        }
    
        $query->statement(
            'SELECT COUNT(uid) AS counts 
             FROM tx_blogext_domain_model_post 
             WHERE deleted="0" AND hidden="0" AND (' . $whereClause . ')',
            $queryParams
        );       $result = $query->execute(true);
        return $result;
    }
        

    public function getCategoryMenu() {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_category WHERE hidden="0" AND deleted="0"');
        $result = $query->execute(true);
        return $result;
    }


    public function findCategoryByUid($uid) {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_blogext_domain_model_category WHERE hidden="0" AND deleted="0" AND uid="'.$uid.'" LIMIT 1');
        $result = $query->execute(true);
        return $result;
    }

}