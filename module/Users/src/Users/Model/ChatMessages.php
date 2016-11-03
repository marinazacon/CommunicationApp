<?php
namespace Users\Model;

class ChatMessages
{
    public $id;
    public $user_id;
    public $message;
    public $stamp;


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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getStamp()
    {
        return $this->stamp;
    }

    /**
     * @param mixed $stamp
     */
    public function setStamp($stamp)
    {
        $this->stamp = $stamp;
    }


}