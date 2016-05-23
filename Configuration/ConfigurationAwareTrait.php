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

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait ConfigurationAwareTrait
{
    /**
     * @return \SymfonyId\AdminBundle\Cache\CacheHandler
     */
    abstract protected function getCacheHandler();

    /**
     * @return ConfiguratorFactory
     */
    abstract protected function getConfiguratorFactory();

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    abstract protected function getKernel();

    /**
     * @param string $controllerClass
     * @return ConfiguratorFactory
     */
    private function getConfiguratorFactoryFroClass($controllerClass)
    {
        if ('prod' !== strtolower($this->getKernel()->getEnvironment())) {
            return $this->getConfiguratorFactory();
        }

        return $this->fetchFromCache($controllerClass);
    }

    /**
     * @param string $controllerClass
     * @return ConfiguratorFactory
     */
    private function fetchFromCache($controllerClass)
    {
        $reflectionController = new \ReflectionClass($controllerClass);
        if (!$this->getCacheHandler()->hasCache($reflectionController)) {
            //It's impossible but we need to prevent and make sure it is not throwing an exception
            return $this->getConfiguratorFactory();
        }

        return require $this->getCacheHandler()->loadCache($reflectionController);
    }
}
