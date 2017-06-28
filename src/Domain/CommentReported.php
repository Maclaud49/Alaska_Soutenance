<?php

namespace Alaska\Domain;

class CommentReported
{
    /**
     * CommentReported id.
     *
     * @var integer
     */
    private $id;

    /**
     * CommentReported counter.
     *
     * @var integer
     */
    private $counter;

    /**
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param int $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Associated comment.
     *
     * @var \Alaska\Domain\Comment
     */
    private $comment;
}