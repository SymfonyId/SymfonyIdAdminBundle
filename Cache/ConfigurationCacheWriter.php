<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\DriverConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Configuration\PageConfigurator;
use SymfonyId\AdminBundle\Configuration\PluginConfigurator;
use SymfonyId\AdminBundle\Configuration\UtilConfigurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationCacheWriter
{
    /**
     * @var CacheHandler
     */
    private $cacheHandler;

    /**
     * @param CacheHandler $cacheHandler
     */
    public function __construct(CacheHandler $cacheHandler)
    {
        $this->cacheHandler = $cacheHandler;
    }

    public function writeCache(\ReflectionClass $reflectionClass, ConfiguratorFactory $configuratorFactory)
    {
        $cache = array();

        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        /** @var DriverConfigurator $driverConfigurator */
        $driverConfigurator = $configuratorFactory->getConfigurator(DriverConfigurator::class);
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        /** @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
        /** @var UtilConfigurator $utilConfigurator */
        $utilConfigurator = $configuratorFactory->getConfigurator(UtilConfigurator::class);

        //Write cache
        $cache['crud'] = $crudConfigurator->getCrud();
        $cache['driver'] = $driverConfigurator->getDriver();
        $cache['grid'] = $gridConfigurator->getGrid();
        $cache['page'] = $pageConfigurator->getPage();
        $cache['plugin'] = $pluginConfigurator->getPlugin();
        $cache['util'] = $utilConfigurator->getUtil();

        $this->cacheHandler->writeCache($reflectionClass, $cache);
    }
}
