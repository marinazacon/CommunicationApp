<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/MailForm.php
namespace Users\Form;
use Zend\Form\Form;

class MailForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Mail');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'fromUserName',
            'attributes' => array(
                'type' => 'label',
                'required' => 'required',
                'disabled' => 'disabled'
            ),
            'options' => array(
                'label' => 'From',
            ),
        ));

        $this->add(array(
            'name' => 'comboToUsers',
            'attributes' => array(
                'type' => 'Select',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'To User',
                'empty_option' => 'Please choose a user',
            ),
        ));

        $this->add(array(
            'name' => 'subject',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Subject',
            ),
        ));

        $this->add(array(
            'name' => 'message',
            'attributes' => array(
                'type' => 'textarea',
                'required' => 'required',
                'rows' => '10'
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));

        $this->add(array(
            'name' => 'sendMail',
            'attributes' => array(
                'type' => 'Submit',
            ),
            'options' => array(
                'label' => 'Send',
            ),
        ));
    }
}