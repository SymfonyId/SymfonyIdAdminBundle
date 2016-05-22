<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\User;

use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyId\AdminBundle\Model\BulkDeletableInterface;
use SymfonyId\AdminBundle\Model\ModelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class User extends BaseUser implements ModelInterface, BulkDeletableInterface
{
    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $avatar;

    /**
     * @var UploadedFile
     */
    protected $file;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    public function setFile(UploadedFile $uploadedFile)
    {
        $this->file = $uploadedFile;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        $roles = $this->getRoles();

        return str_replace(array('ROLE_', '_'), array('', ' '), $roles[0]);
    }

    public function getRoles()
    {
        $roles = parent::getRoles();
        if (1 < count($roles)) {
            array_pop($roles);
        }

        return $roles;
    }

    /**
     * @return string
     */
    public function getDeleteInformation()
    {
        return $this->getUsername();
    }
}
