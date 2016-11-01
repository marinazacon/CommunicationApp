<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/UploadDeleteForm.php
namespace Users\Form;
use Zend\Form\Form;

class UploadDeleteForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('UploadDelete');
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
            'name' => 'yes',
            'attributes' => array(
                'type' => 'Submit',
                'required' => 'required',
                'value' => 'yes',
            ),
            'options' => array(
                'label' => 'Yes',
            ),
        ));

        $this->add(array(
            'name' => 'no',
            'attributes' => array(
                'type' => 'Submit',
                'required' => 'required',
                'value' => 'no',
            ),
            'options' => array(
                'label' => 'No',
            ),
        ));
    }
}