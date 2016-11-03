<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Users\Model\Upload;
use Zend\Form\Element;

class UploadManagerController extends AbstractActionController
{
    public function indexAction()
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $user = $this->getUserInfoFromSession();

        $viewModel = new ViewModel( array(
            'myUploads' => $uploadTable->getUploadsByUserId($user->id),
            'mySharedUploads' => $uploadTable->getSharedUploadsByUserId($user->id),
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

    public function sharingAddAction()
    {
        $user = $this->getUserInfoFromSession();
        $form = $this->getServiceLocator()->get('AddShareForm');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $uploadId = $this->params()->fromRoute('id');

        $select = new Element\Select('comboUser');
        $select->setLabel('Choose User');
        $select->setValueOptions($uploadTable->getUsersNamesForShare($uploadId, $user->id));
        if (empty($uploadTable->getUsersNamesForShare($uploadId, $user->id)))
        {
            $form->get('addShare')->setAttribute('disabled', 'disabled');
        }
        $form->add($select);

        $viewModel = new ViewModel( array(
            'form' => $form,
            'mySharedUsers' => $uploadTable->getSharedUsers($uploadId),
            'upload_id' => $uploadId,
        ));
        return $viewModel;
    }

    public function deleteSharedUserAction()
    {
        $userId = $this->params()->fromRoute('user_id');
        $uploadId = $this->params()->fromRoute('id');

        $this->getServiceLocator()->get('UploadTable')->removeSharing($uploadId, $userId);

        return $this->redirect()->toRoute('users/upload-manager' ,
            array('action' => 'sharingAdd', 'id' => $uploadId));
    }

    public function addSharedUserAction()
    {
        $data = $this->getRequest()->getPost()->toArray();
        $userId = $data['comboUser'];
        $uploadId = $this->params()->fromRoute('id');

        $this->getServiceLocator()->get('UploadTable')->addSharing($uploadId, $userId);

        return $this->redirect()->toRoute('users/upload-manager' ,
            array('action' => 'sharingAdd', 'id' => $uploadId));
    }

    public function fileDownloadAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $upload = $uploadTable->getUpload($uploadId);

        $uploadPath = $this->getFileUploadLocation();
        $file = file_get_contents($uploadPath . $upload->filename);

        $response = $this->getEvent()->getResponse();
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="'
                .$upload->filename . '"',
        ));
        $response->setContent($file);
        return $response;
    }
}