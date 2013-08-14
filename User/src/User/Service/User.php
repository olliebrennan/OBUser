<?php

namespace User\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Stdlib\Hydrator;
use ZfcBase\EventManager\EventProvider;
use ZfcUser\Mapper\UserInterface as UserMapperInterface;
use ZfcUser\Options\UserServiceOptionsInterface;


class User extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var UserMapperInterface
     */
    protected $userMapper;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var Form
     */
    protected $loginForm;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $currentUser;

    protected $authenticatedUser;

    /**
     * @var Hydrator\ClassMethods
     */
    protected $formHydrator;

	public function getUser($userId)
    {
        if (! $currentUser = $this->getUserMapper()->getByUserId((int) $userId)) {
            throw new User\Exception\UserException('User not found');
        }

        $this->setCurrentUser($currentUser);
    }

    /**
     * Change password for this user
     */
    public function changePassword($newCredential)
    {
        $user = $this->getIdentity();
        $changePass = new \User\Entity\ChangePassword();
        $changePass->setSalt($user->getSalt())
            ->setPlainPassword($newCredential);

        $user->setPassword($changePass->getEncryptedPassword());
        $this->getUserMapper()->update($user);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
    }

    /**
     * Sent password reset to the user
     *
     * @param array $postData
     * @param string $url
     * @return boolean
     */
    public function sendReset($postData, $url)
    {
        $user = $this->getServiceManager()->get('user_mapper_passwordreset');
        $emailValidator = new \Zend\Validator\Db\RecordExists(array(
            'table' => 'user',
            'field' => 'email',
            'adapter' => $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')
        ));

        if ($emailValidator->isValid($postData['email'])) {

            // Get the current user
            if (! $currentUser = $this->getUserMapper()->findByEmail($postData['email'])) {
                echo 'unable to find user';
                return false;
            }

            // Build a unique, secure key
            $bcrypt = new Bcrypt;
            $bcrypt->setCost(14);
            $key = md5($bcrypt->create(time() . $currentUser->getEmail()));

            // Generate the reset row
            $user->buildReset($currentUser->getUserId(), $key);

            // Send the email
            $email = $this->getServiceManager()->get('email');
            $config = $email->getConfig();
            $email->to($currentUser->getEmail())
                ->subject('Password Reset')
                ->setTemplate('email/reset')
                ->setType('html')
                ->setVars(array('site_name' => $config['site_name'],
                    'site' => $this->getServiceManager()->get('request')->getServer('HTTP_HOST'),
                    'emailLink' => $url . '/' . $key
                ))
                ->send();

            return true;
        }

        return false;
    }

    /**
     * getUserMapper
     *
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        if (null === $this->userMapper) {
            $this->userMapper = $this->getServiceManager()->get('user_mapper_user');
        }
        return $this->userMapper;
    }

    /**
     * setUserMapper
     *
     * @param $userMapper
     * @return User
     */
    public function setUserMapper($userMapper)
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('cp_user_authservice');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param AuthenticationService $authService
     * @return User
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @return the $currentUser
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

	/**
     * @param field_type $currentUser
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
        return $this;
    }

    /**
     * @return the $currentUser
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

	/**
     * @param field_type $AuthenticatedUser
     */
    public function setAuthenticatedUser($authenticatedUser)
    {
        $this->authenticatedUser = $authenticatedUser;
        return $this;
    }

    public function hasIdentity()
    {
        return (boolean) $this->getAuthService()->hasIdentity();
    }

    public function getIdentity()
    {
        if (! $this->hasIdentity()) {
            return false;
        }

        if ($this->getAuthenticatedUser()) {
            return $this->getAuthenticatedUser();
        }

        if (! $user = $this->getUserMapper()->findByEmail($this->getAuthService()->getIdentity())) {
            throw new User\Exception\UserException('User not found');
        }

        $this->setAuthenticatedUser($user);
        return $user;
    }

    /**
     * Save user to database
     *
     * @param array
     * @return entity
     */
    function save($data)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $data));

        // Get entity
        $entity = $this->getUserMapper()->getEntityPrototype();

        $data = array_merge($this->getUserMapper()->getHydrator()->extract(
            $this->getCurrentUser()
        ), $data);

        // Hydrate entity with passed data
        $this->getUserMapper()->getHydrator()->hydrate($data, $entity);

        if ($entity->getUserId()) {
            $this->getUserMapper()->update($entity);
        } else {
            $this->getUserMapper()->insert($entity);
        }


        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('entity' => $entity));
        return $entity;
    }
}
