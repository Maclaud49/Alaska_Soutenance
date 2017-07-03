<?php

namespace Alaska\Domain;

class Comment 
{
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     *
     * @var \Alaska\Domain\User
     */
    private $author;

    /**
     * Comment content.
     *
     * @var integer
     */
    private $content;

    /**
     * Comment date.
     *
     * @var String
     */
    private $commentDate;

    /**
     * Associated article.
     *
     * @var \Alaska\Domain\Article
     */
    private $article;

    /**
     * Reported counter.
     *
     * @var integer
     */
    private $commentReportedNb;

    /**
     * @return int
     */
    public function getCommentReportedNb()
    {
        return $this->commentReportedNb;
    }

    /**
     * @param $commentReportedNb
     */
    public function setCommentReportedNb($commentReportedNb)
    {
        $this->commentReportedNb = $commentReportedNb;
        return $this;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle(Article $article) {
        $this->article = $article;
        return $this;
    }

    /**
     * @return String
     */
    public function getCommentDate()
    {
        return $this->commentDate;
    }

    /**
     * @param String $commentDate
     */
    public function setCommentDate($commentDate)
    {
        $this->commentDate = $commentDate;
        return $this;
    }


}
