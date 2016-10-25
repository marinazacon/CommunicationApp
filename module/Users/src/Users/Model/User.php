<?php
namespace Users\Model;

class User
{
    public $id;
    public $name;
    public $email;
    public $password;

    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }

    function exchangeArray($data)
    {
        $this->name = (isset($data['name'])) ?
            $data['name'] : null;
        $this->email = (isset($data['email'])) ?
            $data['email'] : null;
        if (isset($data["password"]))
        {
            $this->setPassword($data["password"]);
        }
    }
}