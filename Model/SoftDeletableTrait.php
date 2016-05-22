<?php

/*
 * This file is part of the AdminBundle package.
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
trait SoftDeletableTrait
{
    /**
     * @var bool
     */
    private $isDeleted = false;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var string
     */
    private $deletedBy;

    public function isDeleted($isDeleted = null)
    {
        if (null !== $isDeleted) {
            $this->isDeleted = $isDeleted;
        }

        return $this->isDeleted;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param string $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }
}
