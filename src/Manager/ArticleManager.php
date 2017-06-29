<?php

namespace Alaska\Manager;

use Alaska\Domain\Article;

class ArticleManager extends Manager
{
    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from t_article order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Return a list of all visible articles, sorted by date (most recent first).
     *
     * @return array A list of all visible articles.
     */
    public function findAllVisible() {
        $sql = "select * from t_article where art_visible='1' order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Return a list of all draft articles, sorted by date (most recent first).
     *
     * @return array A list of all draft articles.
     */
    public function findAllDraft() {
        $sql = "select * from t_article where art_visible='0' order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id The article id.
     *
     * @return \Alaska\Domain\Article|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "select * from t_article where art_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("No article matching id " . $id);
        }
    }

    /**
     * Saves an article into the database.
     *
     * @param \Alaska\Domain\Article $article The article to save
     */
    public function save(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent(),
            'art_chapter' => $article->getChapter(),
            'art_visible' => $article->isVisible()
            );

        if ($article->getId()) {
            // The article has already been saved : update it
            $this->getDb()->update('t_article', $articleData, array('art_id' => $article->getId()));
        } else {
            // The article has never been saved : insert it
            $this->getDb()->insert('t_article', $articleData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    /**
     * Removes an article from the database.
     *
     * @param integer $id The article id.
     */
    public function delete($id) {
        // Delete the article
        $this->getDb()->delete('t_article', array('art_id' => $id));
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \Alaska\Domain\Article
     */

    protected function buildDomainObject(array $row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setChapter($row['art_chapter']);
        $article->setVisible($row['art_visible']);
        return $article;
    }

    /**
     * Check the chapter number is available.
     *
     * @param int chapter number.
     * @return boolean
     */

    public function checkChapter($chapter) {
        $sql = "select count(art_chapter) as chapter from t_article where art_chapter=$chapter";
        $result = $this->getDb()->fetchColumn($sql);

        if($result>=1){return false;}
        else return true;

    }
}
