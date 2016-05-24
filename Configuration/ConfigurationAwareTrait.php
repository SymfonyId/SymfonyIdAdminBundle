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
trait ConfigurationAwareTrait
{
    /**
     * @var CacheHandler
     */
    protected $cacheHandler;

    /**
     * @var ConfiguratorFactory
     */
    protected $configuratorFactory;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param CacheHandler $cacheHandler
     */
    public function setCacheHandler(CacheHandler $cacheHandler)
    {
        $this->cacheHandler = $cacheHandler;
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfiguratorFactory(ConfiguratorFactory $configuratorFactory)
    {
        $this->configuratorFactory = $configuratorFactory;
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $controllerClass
     *
     * @return ConfiguratorFactory
     */
    public function getConfiguratorFactory($controllerClass)
    {
        if ('prod' !== strtolower($this->kernel->getEnvironment())) {
            return $this->configuratorFactory;
        }
        $this->configuratorFactory = $this->fetchFromCache($controllerClass);

        return $this->configuratorFactory;
    }

    /**
     * @param string $controllerClass
     *
     * @return ConfiguratorFactory
     */
    private function fetchFromCache($controllerClass)
    {
        $reflectionController = new \ReflectionClass($controllerClass);
        if (!$this->cacheHandler->hasCache($reflectionController)) {
            //It's impossible but we need to prevent and make sure it is not throwing an exception
            return $this->configuratorFactory;
        }

        return require $this->cacheHandler->loadCache($reflectionController);
    }
}
