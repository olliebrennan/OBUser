<?php

namespace User\Entity;

class PasswordReset
{
    protected $userId;

    protected $key;

	/**
     * @return the $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

	/**
     * @return the $key
     */
    public function getKey()
    {
        return $this->key;
    }

	/**
     * @param field_type $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

	/**
     * @param field_type $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

}