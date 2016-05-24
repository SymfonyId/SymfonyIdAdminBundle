<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Cache\CacheHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface ConfigurationAwareInterface
{
    /**
     * @param CacheHandler $cacheHandler
     */
    public function setCacheHandler(CacheHandler $cacheHandler);

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfiguratorFactory(ConfiguratorFactory $configuratorFactory);

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel);

    /**
     * @param string $controllerClass
     *
     * @return ConfiguratorFactory
     */
    public function getConfiguratorFactory($controllerClass);
}