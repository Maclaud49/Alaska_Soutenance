<?php

namespace Alaska\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /**
 * User id.
 *
 * @var integer
 */
    private $id;

    /**
     * User last view article.
     *
     * @var integer
     */
    private $lastViewArt;

    /**
     * @return int
     */
    public function getLastViewArt()
    {
        return $this->lastViewArt;
    }

    /**
     * @param int $lastViewArt
     */
    public function setLastViewArt($lastViewArt)
    {
        $this->lastViewArt = $lastViewArt;
    }

    /**
     * User name.
     *
     * @var string
     */
    private $username;

    /**
     *
     * @var string
     */
    private $email;

    /**
     * User password.
     *
     * @var string
     */
    private $password;

    /**
     * User new password.
     *
     * @var string
     */
    private $passwordNew;

    /**
     * @return string
     */
    public function getPasswordNew()
    {
        return $this->passwordNew;
    }

    /**
     * @param string $passwordNew
     */
    public function setPasswordNew($passwordNew)
    {
        $this->passwordNew = $passwordNew;
    }

    /**
     * Salt that was originally used to encode the password.
     *
     * @var string
     */
    private $salt;

    /**
     * Role.
     * Values : ROLE_USER or ROLE_ADMIN.
     *
     * @var string
     */
    private $role;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    /**
     * User last connection date.
     *
     * @var String
     */
    private $lastConnectedDate;

    /**
     * @return String
     */
    public function getLastConnectedDate()
    {
        return $this->lastConnectedDate;
    }

    /**
     * @param String $lastConnectionDate
     */
    public function setLastConnectedDate($lastConnectedDate)
    {
        $this->lastConnectedDate = $lastConnectedDate;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        // Nothing to do here
    }
}
