<?php
namespace Users\Model;

class User
{
    public $id;
    public $name;
    public $email;
    public $password;


    function exchangeArray($data)
    {
        foreach ($data as $field => $value) {
            $this->$field = (isset($value)) ? $value : null;
        }

        return true;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }

    public function getPassword()
    {
        return $this->password;
    }
}