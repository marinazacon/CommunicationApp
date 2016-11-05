<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/UploadForm.php
namespace Users\Form;
use Zend\Form\Form;

class ImageUploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('ImageUpload');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'required' => 'required'
            ),
        ));

        $this->add(array(
            'name' => 'label',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'File Description',
            ),
        ));

        $this->add(array(
            'name' => 'fileupload',
            'attributes' => array(
                'type' => 'file',
            ),
            'options' => array(
                'label' => 'File Upload',
            ),
        ));

        $this->add(array(
            'name' => 'upload',
            'attributes' => array(
                'type' => 'Submit',
            ),
            'options' => array(
                'label' => 'Upload Now',
            ),
        ));
    }
}