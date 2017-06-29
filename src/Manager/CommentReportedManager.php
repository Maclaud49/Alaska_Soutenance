<?php

namespace Alaska\Manager;

use Alaska\Domain\CommentReported;

class CommentReportedManager extends Manager
{
    /**
     * @var \Alaska\Manager\CommentManager
     */
    private $commentManager;

    public function setCommentManager(CommentManager $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Returns a list of all reported comments, sorted by date (most recent first).
     *
     * @return array A list of all reported comments.
     */
    public function findAll() {
        $sql = "select * from t_comment_reported where com_rep_counter>0 order by com_rep_counter desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['com_rep_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    /**
 * Return a list of all reported comments for an comment
 *
 * @param integer $commentId The comment id.
 *
 * @return array A list of all reported comments for the comment.
 */
    public function findAllByComment($commentId) {
        // The associated comment is retrieved only once
        $comment = $this->commentManager->find($commentId);

        $sql = "select com_rep_id, com_id from t_comment_reported where com_id=? order by com_id";
        $result = $this->getDb()->fetchAll($sql, array($commentId));

        // Convert query result to an array of domain objects
        $commentsReported = array();
        foreach ($result as $row) {
            $comId = $row['com_id'];
            $commentReported = $this->buildDomainObject($row);
            // The associated comment is defined for the constructed reported comment
            $commentReported->setComment($comment);
            $commentsReported[$comId] = $commentReported;
        }
        return $commentsReported;
    }

    /**
     * Return a list of all reported comments for an article
     *
     * @param integer $articleId The comment id.
     *
     * @return array A list of all reported comments for the article.
     */
    public function findAllByArticle($articleId) {


        $sql = "select * from t_comment, t_comment_reported where art_id=? and t_comment.com_id=t_comment_reported.com_id";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

        // Convert query result to an array of domain objects
        $commentsReported = array();
        foreach ($result as $row) {
            $comRepId = $row['com_rep_id'];
            $commentReported = $this->buildDomainObject($row);
            // The associated comment is defined for the constructed reported comment
            $commentsReported[$comRepId] = $commentReported;
        }
        return $commentsReported;
    }

    /**
     * Return a reported comment for an comment
     *
     * @param integer $commentId The comment id.
     *
     * @return a reported comment for the comment.
     */
    public function findByComment($commentId) {
        // The associated comment is retrieved only once
        $comment = $this->commentManager->find($commentId);

        $sql = "select * from t_comment_reported where com_id=?";
        $result = $this->getDb()->fetchAll($sql, array($commentId));

            if($result)
                return $this->buildDomainObject($result[0]);

            else
                return false;

    }

    /**
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \Alaska\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $commentReported = new CommentReported();
        $commentReported->setId($row['com_rep_id']);
        $commentReported->setCounter($row['com_rep_counter']);


        if (array_key_exists('com_id', $row)) {
            // Find and set the associated article
            $comId = $row['com_id'];
            $com = $this->commentManager->find($comId);
            $commentReported->setComment($com);
        }

        return $commentReported;
    }
    /**
     * Saves a reported comment into the database.
     *
     * @param \Alaska\Domain\ReportedComment $commentReported The reported comment to save
     */
    public function save(CommentReported $commentReported) {
        $commentData = array(
            'com_id' => $commentReported->getComment()->getId(),
            'com_rep_counter' => $commentReported->getCounter()
        );

        if ($commentReported->getId()) {
            // The reported comment has already been saved : update counter
            $this->getDb()->update('t_comment_reported', $commentData, array('com_rep_id' => $commentReported->getId()));

        } else {
            // The reported comment has never been saved : insert it
            $this->getDb()->insert('t_comment_reported', $commentData);
            // Get the id of the newly created reported comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $commentReported->setId($id);

        }
    }



    /**
     * Removes all reported comments for a user
     *
     * @param integer $userId The id of the user
     */
    public function deleteAllByComment($comId) {
        $this->getDb()->delete('t_comment_reported', array('com_id' => $comId));
    }

    /**
     * Removes a comment from the database.
     *
     * @param integer $id The reported comment id
     */
    public function delete($id) {
        // Delete the reported comment
        $this->getDb()->delete('t_comment_reported', array('com_id' => $id));
    }
}