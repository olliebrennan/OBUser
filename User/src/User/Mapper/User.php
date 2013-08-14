<?php
namespace User\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class User extends AbstractDbMapper
{
    protected $tableName  = 'user';

    public function findByEmail($email)
    {
        $select = $this->getSelect()->where(array('email' => (string) $email));
        $entity = $this->select($select)->current();
        return $entity;
    }

    public function findByUserId($userId)
    {
        $select = $this->getSelect()->where(array('user_id' => (int) $userId));
        $entity = $this->select($select)->current();
        return $entity;
    }

    public function insert($entity)
    {
        $result = parent::insert($entity);
        $entity->setUserId($result->getGeneratedValue());
        return $result;
    }

    public function update($entity)
    {
        $result = parent::update($entity, array('user_id' => $entity->getUserId()));
        return $result;
    }
}