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

use SymfonyId\AdminBundle\Exception\ConfiguratorNotFound;
use SymfonyId\AdminBundle\Exception\FreezeStateException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfiguratorFactory
{
    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * @var bool
     */
    private $freeze = false;

    /**
     * @param ConfiguratorInterface $configurator
     */
    public function addConfigurator(ConfiguratorInterface $configurator)
    {
        if ($this->freeze) {
            throw new FreezeStateException($this);
        }

        $this->configurators[get_class($configurator)] = $configurator;
    }

    /**
     * @param string $configuratorClass
     *
     * @return ConfiguratorInterface
     */
    public function getConfigurator($configuratorClass)
    {
        if (!array_key_exists($configuratorClass, $this->configurators)) {
            throw new ConfiguratorNotFound($configuratorClass);
        }

        return $this->configurators[$configuratorClass];
    }

    public function freeze()
    {
        $this->freeze = true;
    }
}
