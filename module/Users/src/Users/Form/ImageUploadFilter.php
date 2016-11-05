<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/ImageUploadFilter.php
namespace Users\Form;
use Zend\InputFilter\InputFilter;

class ImageUploadFilter extends InputFilter
{
    public function __construct($name = null)
    {

        $this->add(array(
            'name' => 'label',
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
            'name' => 'fileupload',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\File\Size',
                    'options' => array(
                        'min' => 120,
                        'max' => 200000,
                    ),
                ),
                array(
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => array(
                        'extension' => array( 'png', 'jpeg', 'jpg', 'gif')
                    ),
                ),
            ),
        ));
    }
}