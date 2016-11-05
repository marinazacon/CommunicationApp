<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

class ImageUploadTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveImageUpload(ImageUpload $image_upload)
    {
        $data = array(
            'filename' => $image_upload->filename,
            'label' => $image_upload->label,
            'thumbnail' => $image_upload->thumbnail,
            'user_id' => $image_upload->user_id,
        );
        $id = (int)$image_upload->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getImageUpload($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Image upload ID does not exist');
            }
        }
    }

    public function getImageUpload($id)
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

    public function getImageUploadsByUserId($userId)
    {
        $userId = (int) $userId;
        $rowset = $this->tableGateway->select(
            array('user_id' => $userId));
        return $rowset;
    }

    public function deleteImageUpload($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}