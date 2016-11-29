<?php
/**
 * Created by PhpStorm.
 * User: marina
 * Date: 23/10/16
 * Time: 16.05
 */

// filename : module/Users/src/Users/Form/SearchForm.php
namespace Users\Form;
use Zend\Form\Form;

class SearchForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Search');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'query',
            'attributes' => array(
                'type' => 'text',
                'id' => 'queryText',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Search String',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search'
            ),
        ));
    }
}