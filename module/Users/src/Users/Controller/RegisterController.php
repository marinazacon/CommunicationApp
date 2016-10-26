<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\RegisterForm;
use Users\Form\RegisterFilter;
use Users\Model\User;
use Users\Model\UserTable;


class RegisterController extends AbstractActionController
{
    public function indexAction()
    {
        $form = $this->getServiceLocator()->get('RegisterForm');
        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }
    public function confirmAction()
    {
        $viewModel = new ViewModel();
        return $viewModel;
    }
    public function processAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL ,
                array( 'controller' => 'register',
                    'action' => 'index'
                ));
        }
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('RegisterForm');
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form' => $form,
            ));
            $model->setTemplate('users/register/index');
            return $model;
        }
        // Create user
        $this->createUser($form->getData());
        return $this->redirect()->toRoute(NULL , array(
            'controller' => 'register',
            'action' => 'confirm'
        ));
    }

    protected function createUser(array $data)
    {
        $user = new User();
        $user->exchangeArray($data);
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userTable->saveUser($user);
        return true;
    }
}