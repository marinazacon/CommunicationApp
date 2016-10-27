<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Users\Model\User;


class UserManagerController extends AbstractActionController
{
    public function indexAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $viewModel = new ViewModel(array('users' => $userTable->fetchAll()));
        return $viewModel;
    }

    public function confirmAction()
    {
        $viewModel = new ViewModel();
        return $viewModel;
    }

    public function editAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('UserEditForm');
        $form->bind($user);
        $viewModel = new ViewModel(array(
            'form' => $form,
            'id' => $this->params()->fromRoute('id')
        ));
        return $viewModel;
    }

    public function processAction()
    {
        // Get User details from POST
        $post = $this->request->getPost();

        // Load User entity
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($post->id);

        $user->name = $post->name;
        $user->email = $post->email;

        // Save user
        $this->getServiceLocator()->get('UserTable')->saveUser($user);

        return $this->redirect()->toRoute(NULL , array(
            'controller' => 'user-manager',
            'action' => 'confirm'
        ));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('yes');

            if ($delete == 'yes') {
                $this->getServiceLocator()->get('UserTable')->deleteUser($this->params()->fromRoute('id'));
            }

            return $this->redirect()->toRoute(NULL , array(
                'controller' => 'user-manager',
                'action' => 'index'
            ));
        }
    }

    public function confirmDeleteAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('UserDeleteForm');

        $viewModel = new ViewModel(array(
            'form' => $form,
            'id' => $this->params()->fromRoute('id'),
            'name' => $user->name
        ));
        return $viewModel;
    }

    public function userAddAction()
    {
        $form = $this->getServiceLocator()->get('UserAddForm');
        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }

    public function userAddProcessAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL ,
                array( 'controller' => 'user-manager',
                    'action' => 'index'
                ));
        }
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('UserAddForm');
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form' => $form,
            ));
            $model->setTemplate('users/user-manager/user-add');
            return $model;
        }
        // Create user
        $this->createUser($form->getData());
        return $this->redirect()->toRoute(NULL , array(
            'controller' => 'user-manager',
            'action' => 'index'
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