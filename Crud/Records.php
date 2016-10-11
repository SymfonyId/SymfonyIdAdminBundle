<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Records
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $identifier;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param array $identifier
     */
    public function setIdentifier(array $identifier)
    {
        $this->identifier = $identifier;
    }
}
