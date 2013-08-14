<?php

namespace User\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result as AuthenticationResult;

class Adapter implements AdapterInterface
{
    protected $identity;

    protected $credential;

    protected $account;

    protected $dbAdapter;

    protected $userMapper;

	/**
     * @return the $userMapper
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }

	/**
     * @param field_type $userMapper
     */
    public function setUserMapper($userMapper)
    {
        $this->userMapper = $userMapper;
    }

	/**
     * @return the $dbadapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

	/**
     * @param field_type $dbadapter
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

	/**
     * @return string $account
     */
    public function getAccount()
    {
        return $this->account;
    }

	/**
     * @return string $identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

	/**
     * @return string $credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

	/**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

	/**
     * @param string $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

	/**
     * @param string $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

	/**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        if (
            ! ($identity = $this->getIdentity()) ||
            ! ($cred = $this->getCredential()) ||
//             ! ($account = $this->getAccount()) ||
            ! ($user = $this->getUserMapper()->findByEmail($identity))) {

            return $this->getAuthResult();
        }

        $passCreator = new \User\Entity\ChangePassword();
        $passCreator->setSalt($user->getSalt())
            ->setPlainPassword($cred);
        if ($user->getPassword() !== $passCreator->getEncryptedPassword()) {
            return $this->getAuthResult();
        }

        return $this->getAuthResult(AuthenticationResult::SUCCESS, $user->getUserId());
    }

    protected function getAuthResult($result = AuthenticationResult::FAILURE, $identity = null)
    {
        return new AuthenticationResult(
            $result,
            ($identity ?: $this->getIdentity()),
            array()
        );
    }
}