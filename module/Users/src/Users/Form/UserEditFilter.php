<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/UserEditFilter.php
namespace Users\Form;
use Zend\InputFilter\InputFilter;

class UserEditFilter extends InputFilter
{
    public function __construct($name = null)
    {


        $this->add(array(
            'name' => 'id   ',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'name',
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
                        'max' => 140,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));

    }
}