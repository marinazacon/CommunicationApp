<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

class ChatMessagesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveChatMessages(ChatMessages $chatMessage)
    {
        $data = array(
            'user_id' => $chatMessage->user_id,
            'message' => $chatMessage->message,
            'stamp' => NULL,
        );
        $id = (int)$chatMessage->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getChatMessage($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Chat Message ID does not exist');
            }
        }
    }

    public function getChatMessage($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getChatMessagesByUserId($userId)
    {
        $userId = (int) $userId;
        $rowset = $this->tableGateway->select(
            array('user_id' => $userId));
        return $rowset;
    }

    public function deleteChatMessage($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}