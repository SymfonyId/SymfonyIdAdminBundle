<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Manager;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Exception\DriverNotFoundException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ManagerFactory
{
    /**
     * @var ManagerInterface[]
     */
    private $managers;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param ManagerInterface $manager
     */
    public function addManager(ManagerInterface $manager)
    {
        $this->managers[$manager->getDriver()] = $manager;
    }

    /**
     * @param Driver $driver
     *
     * @return ManagerInterface
     *
     * @throws DriverNotFoundException
     */
    public function getManager(Driver $driver)
    {
        if (!in_array($driver->getDriver(), array_keys($this->managers))) {
            throw new DriverNotFoundException($driver->getDriver());
        }

        $manager = $this->managers[$driver->getDriver()];
        $manager->setModelClass($this->modelClass);

        return $manager;
    }
}
