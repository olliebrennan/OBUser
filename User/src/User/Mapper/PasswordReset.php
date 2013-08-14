<?php
namespace User\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class PasswordReset extends AbstractDbMapper
{
    protected $tableName = 'password_reset';

    public function buildReset($userId, $key)
    {
        $userValidator = new \Zend\Validator\Db\RecordExists(array(
            'table' => $this->tableName, 'field' => 'user_id', 'adapter' => $this->getDbAdapter()
        ));

        if (! $userValidator->isValid($userId)) {
            $query = $this->getSql()
                ->insert($this->tableName)
                ->values(array('user_id' => $userId, 'key' => $key));
        }
        else {
            $query = $this->getSql()
                ->update($this->tableName)
                ->set(array('key' => $key))
                ->where(array('user_id' => $userId));
        }

        $this->getSql()->prepareStatementForSqlObject($query)->execute();
        return $key;
    }

    public function findByResetKey($key)
    {
        $select = $this->getSelect()->where(array('key' => (string) $key));
        $entity = $this->select($select)->current();

        if ($entity) {
            return $entity->getUserId();
        }

        return false;
    }

    /**
     * Delete reset key for this user
     *
     * @param string $key
     */
    public function changePassword($resetKey, $userMapper, $newCredential)
    {
        if (! $key = $this->findByResetKey($resetKey)) {
            throw new \Exception('Unable to remove as reset key does not exist');
        }

        $user = $userMapper->findByUserId($key);
        $changePass = new \User\Entity\ChangePassword();
        $changePass->setSalt($user->getSalt())
            ->setPlainPassword($newCredential);

        $user->setPassword($changePass->getEncryptedPassword());
        $userMapper->update($user);

        return parent::delete(array('key' => $resetKey));
    }

}