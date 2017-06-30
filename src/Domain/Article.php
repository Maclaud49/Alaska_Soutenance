<?php

namespace Alaska\Domain;

class Article
{
    /**
     * Article id.
     *
     * @var integer
     */
    private $id;

    /**
     * Article comments number.
     *
     * @var integer
     */
    private $commentsNb;

    /**
     * Article views number.
     *
     * @var integer
     */
    private $viewsNb;

    /**
     * Article last updated date.
     *
     * @var date
     */
    private $lastUpdatedDate;

    /**
     * @return date
     */
    public function getLastUpdatedDate()
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @param date $lastUpdatedDate
     */
    public function setLastUpdatedDate($lastUpdatedDate)
    {
        $this->lastUpdatedDate = $lastUpdatedDate;
    }

    /**
     * @return int
     */
    public function getViewsNb()
    {
        return $this->viewsNb;
    }

    /**
     * @param int $viewsNb
     */
    public function setViewsNb($viewsNb)
    {
        $this->viewsNb = $viewsNb;
    }

    /**
     * @return int
     */
    public function getCommentsNb()
    {
        return $this->commentsNb;
    }

    /**
     * @param int $commentsNb
     */
    public function setCommentsNb($commentsNb)
    {
        $this->commentsNb = $commentsNb;
    }

    /**
     * Article title.
     *
     * @var string
     */
    private $title;

    /**
     * Article content.
     *
     * @var string
     */
    private $content;

    /**
     * Article visibility.
     *
     * @var boolean
     */
    private $visible;

    /**
     * @return bool
     */
    public function isVisible()
    {
        if($this->visible==0){
            return false;
        }
        else return true;
    }

    /**
     * @param bool $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return int
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * @param int $chapter
     */
    public function setChapter($chapter)
    {
        $this->chapter = $chapter;
    }

    /**
     * Article chapter.
     *
     * @var int
     */
    private $chapter;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
}
