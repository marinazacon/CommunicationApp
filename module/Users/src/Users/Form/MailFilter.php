<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/MailFilter.php
namespace Users\Form;
use Zend\InputFilter\InputFilter;

class MailFilter extends InputFilter
{
    public function __construct($name = null)
    {

        $this->add(array(
            'name' => 'subject',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 100,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'message',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 1000,
                    ),
                ),
            ),
        ));
    }
}