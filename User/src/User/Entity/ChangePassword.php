<?php
namespace User\Entity;

use Zend\Crypt\Password\Bcrypt;

class ChangePassword
{
    protected $plainPassword;

    protected $salt;

    /**
     * Stops password decryption if DB is attached - DO NOT CHANGE! EVER!
     *
     * @var string
     */
    protected $localSalt = 'ppPA$$13204eight84%^thorseCAMELlampL3M0N';

	/**
     * @return string $encryptedPassword
     */
    public function getEncryptedPassword()
    {
        if (empty($this->plainPassword)) {
            throw new \Exception('Cannot encrypt password. Components missing: pass');
        }
        if (empty($this->salt)) {
            throw new \Exception('Cannot encrypt password. Components missing: salt');
        }
        if (empty($this->localSalt)) {
            throw new \Exception('Cannot encrypt password. Components missing: localSalt');
        }

        $bcrypt = new Bcrypt;
        $bcrypt->setCost(14)
            ->setSalt($this->salt . $this->localSalt);
        return $bcrypt->create($this->plainPassword);
    }

	/**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

	/**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }
}