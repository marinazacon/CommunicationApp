<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

class UploadTable
{
    protected $tableGateway;
    protected $uploadSharingTableGateway;

    public function __construct(TableGateway $tableGateway, TableGateway $uploadSharingTableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->uploadSharingTableGateway = $uploadSharingTableGateway;
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
        $this->uploadSharingTableGateway->delete(array('upload_id' => $id));
    }

    public function addSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id' => (int)$userId,
        );
        $this->uploadSharingTableGateway->insert($data);
    }
    public function removeSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id' => (int)$userId,
        );
        $this->uploadSharingTableGateway->delete($data);
    }

    public function getSharedUsers($uploadId)
    {
        $uploadId = (int) $uploadId;
        $rowset = $this->uploadSharingTableGateway->select(
            function (\Zend\Db\Sql\Select $select) use ($uploadId){
                $select->join('user', 'uploads_sharing.user_id = user.id',array ('id', 'name', 'email'));
                $select->where(array('uploads_sharing.upload_id'=>$uploadId));
            });
        return $rowset;
    }

    public function getSharedUploadsByUserId($userId)
    {
        $userId = (int) $userId;

        $rowset = $this->uploadSharingTableGateway->select(
            function (\Zend\Db\Sql\Select $select) use ($userId){
                $select->join('uploads', 'uploads_sharing.upload_id = uploads.id',array ('label', 'filename', 'user_id'));
                $select->join('user', 'uploads.user_id = user.id',array ('name', 'email'));
                $select->where(array('uploads_sharing.user_id'=>$userId));
            });
        return $rowset;
    }

    public function getUsersNamesForShare($uploadId, $userID)
    {
        $uploadId = (int) $uploadId;
        $userID = (int) $userID;
        $onExpression = new \Zend\Db\Sql\Expression('uploads_sharing.user_id = user.id and uploads_sharing.upload_id = ' . $uploadId);

        $rowset = $this->uploadSharingTableGateway->select(
            function (\Zend\Db\Sql\Select $select) use ($onExpression, $userID){
                $select->join('user', $onExpression, array('*'), \Zend\Db\Sql\Select::JOIN_RIGHT);
                $select->where->isNull('uploads_sharing.user_id');
                $select->where->notEqualTo('user.id', $userID);
            });

        $users_names = array();
        foreach ($rowset as $row)
        {
            $users_names[(string)$row->id] = $row->name;
        }
        return $users_names;
    }
}