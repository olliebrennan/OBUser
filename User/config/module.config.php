<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'user_login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'user_logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'user_recover_password' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/recover-password',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'recover-password',
                    ),
                ),
            ),
            'user_reset_password' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/reset-password[/:key]',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'reset-password',
                    ),
                ),
            ),
            'user_reset_password_success' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/reset-password/success',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'reset-password-success',
                    ),
                ),
            ),
            'user_login_authenticate' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login/auth',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'authenticate',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Auth' => 'User\Controller\AuthController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'user/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'email/reset'           => __DIR__ . '/../view/email/reset.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'user_role' => function ($sm) {
                if ($sm->get('cp_user_service')->hasIdentity()) {
                    return $sm->get('cp_user_service')->getIdentity()->getRole();
                } else {
                    return 'guest';
                }
            },
            'user_login_form' => function($sm) {
                $form = new User\Form\Login();
                $form->setInputFilter(new User\Form\LoginFilter());
                return $form;
            },
            'user_recover_form' => function($sm) {
                $form = new User\Form\Recover();
                $form->setInputFilter(new User\Form\RecoverFilter());
                return $form;
            },
            'user_resetpassword_form' => function($sm) {
                $form = new User\Form\ResetPassword();
                $form->setInputFilter(new User\Form\ResetPasswordFilter());
                return $form;
            },
            'user_mapper_user' => function($sm) {
                $mapper = new User\Mapper\User();
                $mapper->setEntityPrototype(new User\Entity\User());
                $mapper->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                return $mapper;
            },
            'user_mapper_passwordreset' => function($sm) {
                $mapper = new User\Mapper\PasswordReset();
                $mapper->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'))
                    ->setEntityPrototype(new User\Entity\PasswordReset());
                return $mapper;
            },
            'cp_user_auth_storage' => function($sm){
                return new User\Auth\AuthStorage('cp_session_store');
            },
            'cp_user_authservice' => function($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $authAdapter  = new User\Auth\Adapter();
                $authAdapter->setDbAdapter($dbAdapter);
                $authAdapter->setUserMapper($sm->get('user_mapper_user'));
                $authService = new Zend\Authentication\AuthenticationService();
                $authService->setAdapter($authAdapter);
                $authService->setStorage($sm->get('cp_user_auth_storage'));
                return $authService;
            },
            'cp_user_service' => function($sm) {
                $service = new User\Service\User();
                $service->setAuthService($sm->get('cp_user_authservice'))
                    ->setUserMapper($sm->get('user_mapper_user'));
                return $service;
            },
        ),
    ),
);
