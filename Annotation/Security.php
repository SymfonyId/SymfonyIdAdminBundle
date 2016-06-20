<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("create", type = "string"),
 *   @Attribute("edit", type = "string"),
 *   @Attribute("show", type = "string"),
 *   @Attribute("delete", type = "string"),
 *   @Attribute("list", type = "string"),
 *   @Attribute("download", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Security
{
    const DEFAULT_ROLE = 'ROLE_USER';

    /**
     * @var string
     */
    private $create = self::DEFAULT_ROLE;

    /**
     * @var string
     */
    private $read = self::DEFAULT_ROLE;

    /**
     * @var string
     */
    private $edit = self::DEFAULT_ROLE;

    /**
     * @var string
     */
    private $delete = self::DEFAULT_ROLE;

    /**
     * @var string
     */
    private $download = self::DEFAULT_ROLE;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['create'])) {
            $this->create = $data['create'];
        }

        if (isset($data['edit'])) {
            $this->edit = $data['edit'];
        }

        if (isset($data['delete'])) {
            $this->delete = $data['delete'];
        }

        if (isset($data['read'])) {
            $this->read = $data['read'];
        }

        if (isset($data['download'])) {
            $this->download = $data['download'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @return string
     */
    public function getEdit()
    {
        return $this->edit;
    }

    /**
     * @return string
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @return string
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @return string
     */
    public function getDownload()
    {
        return $this->download;
    }
}
