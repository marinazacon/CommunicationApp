<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

class UploadTable
{
    protected $tableGateway;
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveUpload(Upload $upload)
    {
        $data = array(
            'filename' => $upload->filename,
            'label' => $upload->label,
            'user_id' => $upload->user_id,
        );
        $id = (int)$upload->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUpload($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User ID does not exist');
            }
        }
    }

    public function getUpload($id)
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

    public function getUploadsByUserId($userId)
    {
        $userId = (int) $userId;
        $rowset = $this->tableGateway->select(
            array('user_id' => $userId));
        return $rowset;
    }

    public function deleteUpload($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}