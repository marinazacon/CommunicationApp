<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Users\Model\ImageUpload;
use Zend\Form\Element;

class MediaManagerController extends AbstractActionController
{
    public function indexAction()
    {
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $user = $this->getUserInfoFromSession();

        $viewModel = new ViewModel( array(
            'myImageUploads' => $uploadTable->getImageUploadsByUserId($user->id),
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

    public function imageUploadAddAction()
    {
        $form = $this->getServiceLocator()->get('ImageUploadForm');
        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }

    public function processAction()
    {
        $form = $this->getServiceLocator()->get('ImageUploadForm');

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
                $exchange_data['thumbnail'] = $this->generateThumbnail($uploadFile['name']);
                $exchange_data['user_id'] = $this->getUserInfoFromSession()->id;

                $upload = new ImageUpload();
                $upload->exchangeArray($exchange_data);
                $this->getServiceLocator()->get('ImageUploadTable')->saveImageUpload($upload);

                return $this->redirect()->toRoute('users/media-manager' ,
                        array('action' => 'index'));
            }
        }
        $viewModel = new ViewModel(array('form' => $form));
        $viewModel->setTemplate('users/media-manager/image-upload-add');
        return $viewModel;
    }

    public function getFileUploadLocation()
    {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function generateThumbnail($imageFileName)
    {
        $path = $this->getFileUploadLocation();
        $sourceImageFileName = $path . '/' . $imageFileName;
        $thumbnailFileName = 'tn_' . $imageFileName;
        $imageThumb = $this->getServiceLocator()->get('WebinoImageThumb');
        $thumb = $imageThumb->create($sourceImageFileName, $options = array());
        $thumb->resize(75, 75);
        $thumb->save($path . '/' . $thumbnailFileName);
        return $thumbnailFileName;
    }

    public function showImageAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getImageUpload($uploadId);

        // Fetch Configuration from Module Config
        $uploadPath = $this->getFileUploadLocation();
        if ($this->params()->fromRoute('subaction') == 'thumb')
        {
            $filename = $uploadPath ."/" . $upload->thumbnail;
        } else {
            $filename = $uploadPath . $upload->filename;
        }
        $file = file_get_contents($filename);

        // Directly return the Response
        $response = $this->getEvent()->getResponse();
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="'
                .$upload->filename . '"',
        ));
        $response->setContent($file);

        return $response;
    }

    public function showFullImageAction()
    {
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $viewModel = new ViewModel( array(
            'upload' => $uploadTable->getImageUpload($this->params()->fromRoute('id')),
        ));
        return $viewModel;
    }

    public function confirmDeleteAction()
    {
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getImageUpload($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('ImageUploadDeleteForm');

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
                $upload = $this->getServiceLocator()->get('ImageUploadTable')->getImageUpload($this->params()->fromRoute('id'));
                $uploadPath = (String)$uploadPath . (String)$upload->filename;
                unlink($uploadPath);
                $uploadPath = (String)$uploadPath . (String)$upload->thumbnail;
                unlink($uploadPath);
                $this->getServiceLocator()->get('ImageUploadTable')->deleteImageUpload($upload->id);
            }

            return $this->redirect()->toRoute(NULL , array(
                'controller' => 'media-manager',
                'action' => 'index'
            ));
        }
    }
}
