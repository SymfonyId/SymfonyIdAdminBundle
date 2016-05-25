<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Model;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait TimestampAwareTrait
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $updatedBy;

    /**
     * @param \DateTime $date
     */
    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $date
     */
    public function setUpdatedAt(\DateTime $date)
    {
        $this->updatedAt = $date;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $username
     */
    public function setCreatedBy($username)
    {
        $this->createdBy = $username;
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string $username
     */
    public function setUpdatedBy($username)
    {
        $this->updatedBy = $username;
    }

    /**
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
