<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Users\Model\Upload;


class UploadManagerController extends AbstractActionController
{
    public function indexAction()
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $user = $this->getUserInfoFromSession();

        $viewModel = new ViewModel( array(
            'myUploads' => $uploadTable->getUploadsByUserId($user->id),
        ));
        return $viewModel;
    }

    public function getUserInfoFromSession()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $authService = $this->getServiceLocator()->get('AuthService');

        $userEmail = $authService->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);

        return $user;
    }

    public function uploadAddAction()
    {
        $form = $this->getServiceLocator()->get('UploadForm');
        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }

    public function processAction()
    {
        $form = $this->getServiceLocator()->get('UploadForm');

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form->setData($data);

        if ($form->isValid()) {
            $uploadFile = $this->params()->fromFiles('fileupload');
            $uploadPath = $this->getFileUploadLocation();

            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->addFilter('Rename', array('target' => $uploadPath . $uploadFile['name'], 'overwrite' => true));

            if ($adapter->receive('fileupload')) {
                $exchange_data = array();
                $exchange_data['label'] = $form->get('label')->getValue();
                $exchange_data['filename'] = $uploadFile['name'];
                $exchange_data['user_id'] = $this->getUserInfoFromSession()->id;

                $upload = new Upload();
                $upload->exchangeArray($exchange_data);
                $this->getServiceLocator()->get('UploadTable')->saveUpload($upload);

                return $this->redirect()->toRoute('users/upload-manager' ,
                        array('action' => 'index'));
            }
        }
        $viewModel = new ViewModel(array('form' => $form));
        $viewModel->setTemplate('users/upload-manager/upload-add');
        return $viewModel;
    }

    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function confirmDeleteAction()
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $upload = $uploadTable->getUpload($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('UploadDeleteForm');

        $viewModel = new ViewModel(array(
            'form' => $form,
            'id' => $this->params()->fromRoute('id'),
            'label' => $upload->label
        ));
        return $viewModel;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('yes');

            if ($delete == 'yes') {
                $uploadPath = $this->getFileUploadLocation();
                $upload = $this->getServiceLocator()->get('UploadTable')->getUpload($this->params()->fromRoute('id'));
                $uploadPath = (String)$uploadPath . (String)$upload->filename;
                unlink($uploadPath);
                $this->getServiceLocator()->get('UploadTable')->deleteUpload($upload->id);
            }

            return $this->redirect()->toRoute(NULL , array(
                'controller' => 'upload-manager',
                'action' => 'index'
            ));
        }
    }

}