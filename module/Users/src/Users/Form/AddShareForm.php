<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/AddShareForm.php
namespace Users\Form;
use Zend\Form\Form;

class AddShareForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('AddShare');
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
            'name' => 'comboUser',
            'attributes' => array(
                'type' => 'Select',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Choose User',
                'empty_option' => 'Please choose a user',
            ),
        ));

        $this->add(array(
            'name' => 'addShare',
            'attributes' => array(
                'type' => 'Submit',
            ),
            'options' => array(
                'label' => 'Add Share',
            ),
        ));
    }
}