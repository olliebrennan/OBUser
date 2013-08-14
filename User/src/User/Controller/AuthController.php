<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    /**
     * Setup controller. Set custom layout
     */
    public function __construct()
    {
        $this->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controller->layout('user/layout');
        });
    }

    public function loginAction()
    {
        if ($this->getServiceLocator()->get('cp_user_service')->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        return new ViewModel(array(
            'form' => $this->getServiceLocator()->get('user_login_form'),
            'messages'  => $this->flashmessenger()->getMessages())
        );
    }

    public function authenticateAction()
    {
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('user_login_form');
        $redirect = 'user_login';

        if ($request->isPost()) {
            $authService = $this->getServiceLocator()->get('cp_user_service')->getAuthService();
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $authService->getAdapter()
                    ->setIdentity($request->getPost('identity'))
                    ->setCredential($request->getPost('credential'));
                $result = $authService->authenticate();

                if ($result->isValid()) {
                    $redirect = 'home';
                    //check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1 ) {
                        $this->getSessionStorage()
                            ->setRememberMe(1);
                        //set storage again
                        $authService->setStorage($this->getSessionStorage());
                    }
                    $authService->getStorage()->write($request->getPost('identity'));
                }
            }

            $this->flashmessenger()->addMessage('Invalid email / password combination');
        }

        return $this->redirect()->toRoute($redirect);
    }

    public function recoverPasswordAction()
    {
        $notification = '';
        $success = '';
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('user_recover_form');

        if ($request->isPost()) {
            $postData = $this->params()->fromPost();
            $user = $this->getServiceLocator()->get('cp_user_service');

            if ($user->sendReset($postData, $this->url()->fromRoute('user_reset_password'))) {
                $success = 'A password reset e-mail has been e-mailed to you';
            } else {
                $notification = 'Unable to find account or invalid email provided';
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'notification' => $notification,
            'success' => $success,
        ));
    }

    public function logoutAction()
    {
        $this->getServiceLocator()->get('cp_user_service')->getAuthService()->clearIdentity();
        return $this->redirect()->toRoute('user_login');
    }

    public function resetPasswordSuccessAction()
    {
        return new ViewModel();
    }

    public function resetPasswordAction()
    {
        $request = $this->getRequest();
        $key = $this->params()->fromRoute('key');
        $form = $this->getServiceLocator()->get('user_resetpassword_form');
        $resetMapper = $this->getServiceLocator()->get('user_mapper_passwordreset');

        // Find user by reset key - throw 404 if not found
        if (! $checkReset = $resetMapper->findByResetKey($key)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // User has attempted to change the password
        if ($request->isPost()) {
            $form->setData($this->params()->fromPost());

            // Passwords don't match
            if (! $form->isValid()) {
                return new ViewModel(array(
                    'form' => $form,
                    'notifications'=> $form->getMessages(),
                    'key' => $key,
                ));
            }
            $validatedData = $form->getData();
            $resetMapper->changePassword(
                $key,
                $this->getServiceLocator()->get('user_mapper_user'),
                $validatedData['confirmPassword']
            );
            return $this->redirect()->toRoute('user_reset_password_success');
        }

        return new ViewModel(array(
            'form' => $form,
            'key' => $key,
        ));
    }

    /**
     * Returns the storage session
     *
     * @return User\Auth\AuthStorage
     */
    protected function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()
            ->get('cp_user_auth_storage');
        }

        return $this->storage;
    }
}
