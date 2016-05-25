<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use SymfonyId\AdminBundle\Annotation\Driver;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DriverConfigurator implements ConfiguratorInterface
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param Driver $driver
     */
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }
}
