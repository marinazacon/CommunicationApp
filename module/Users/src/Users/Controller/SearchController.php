<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;


class SearchController extends AbstractActionController
{

    public function indexAction()
    {
        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene\Lucene::create($searchIndexLocation);
        $userTable = $this->getServiceLocator()->get('UserTable');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $allUploads = $uploadTable->fetchAll();
        foreach($allUploads as $fileUpload) {

            $uploadOwner = $userTable->getUser($fileUpload->user_id);
// create lucene fields
            $fileUploadId = Document\Field::unIndexed(
                            'upload_id', $fileUpload->id);
            $label = Document\Field::Text(
                            'label', $fileUpload->label);
            $owner = Document\Field::Text(
                            'owner', $uploadOwner->name);
// create a new document and add all fields
            $indexDoc = new Lucene\Document();
            $indexDoc->addField($label);
            $indexDoc->addField($owner);
            $indexDoc->addField($fileUploadId);
            $index->addDocument($indexDoc);
        }
        $index->commit();

        $view = new ViewModel();
        return $view;
    }

    public function getIndexLocation()
    {
// Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');

        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        if (!empty($config['module_config']['search_index'])) {
            return $config['module_config']['search_index'];
        } else {
            return FALSE;
        }
    }
}