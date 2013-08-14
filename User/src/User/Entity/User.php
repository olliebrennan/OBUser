<?php
namespace User\Entity;

class User
{
    protected $userId;

    protected $email;

    protected $password;

    protected $firstname;

    protected $surname;

    protected $role;

    protected $account;

    protected $salt;

	/**
     * @return the $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

	/**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

	/**
     * @return the $password
     */
    public function getPassword()
    {
        return $this->password;
    }

	/**
     * @return the $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

	/**
     * @return the $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

	/**
     * @return the $role
     */
    public function getRole()
    {
        return $this->role;
    }

	/**
     * @return the $account
     */
    public function getAccount()
    {
        return $this->account;
    }

	/**
     * @param field_type $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

	/**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

	/**
     * @param field_type $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

	/**
     * @param field_type $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

	/**
     * @param field_type $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

	/**
     * @param field_type $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

	/**
     * @param field_type $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

	/**
     * @return the $salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

	/**
     * @param field_type $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}