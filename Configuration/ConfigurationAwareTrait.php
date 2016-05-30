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
        if (!$this->isProduction()) {
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

        return $this->bind(require $this->cacheHandler->loadCache($reflectionController));
    }

    /**
     * @param ConfiguratorInterface[] $configurations
     *
     * @return ConfiguratorFactory
     */
    private function bind(array $configurations)
    {
        $configuratorFactory = clone $this->configuratorFactory;

        if (isset($configurations['crud'])) {
            /** @var CrudConfigurator $crudConfigurator */
            $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
            $crudConfigurator->setCrud($configurations['crud']);
            $configuratorFactory->addConfigurator($crudConfigurator);
        }

        if (isset($configurations['driver'])) {
            /** @var DriverConfigurator $driverConfigurator */
            $driverConfigurator = $configuratorFactory->getConfigurator(DriverConfigurator::class);
            $driverConfigurator->setDriver($configurations['driver']);
            $configuratorFactory->addConfigurator($driverConfigurator);
        }

        if (isset($configurations['grid'])) {
            /** @var GridConfigurator $gridConfigurator */
            $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
            $gridConfigurator->setGrid($configurations['grid']);
            $configuratorFactory->addConfigurator($gridConfigurator);
        }

        if (isset($configurations['page'])) {
            /** @var PageConfigurator $pageConfigurator */
            $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
            $pageConfigurator->setPage($configurations['page']);
            $configuratorFactory->addConfigurator($pageConfigurator);
        }

        if (isset($configurations['plugin'])) {
            /** @var PluginConfigurator $pluginConfigurator */
            $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
            $pluginConfigurator->setPlugin($configurations['plugin']);
            $configuratorFactory->addConfigurator($pluginConfigurator);
        }

        if (isset($configurations['util'])) {
            /** @var UtilConfigurator $utilConfigurator */
            $utilConfigurator = $configuratorFactory->getConfigurator(UtilConfigurator::class);
            $utilConfigurator->setUtil($configurations['util']);
            $configuratorFactory->addConfigurator($utilConfigurator);
        }

        return $configuratorFactory;
    }

    /**
     * @return bool
     */
    private function isProduction()
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return true;
        }

        return false;
    }
}
