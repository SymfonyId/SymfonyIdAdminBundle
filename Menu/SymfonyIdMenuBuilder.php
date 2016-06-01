<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Menu;

use SymfonyId\AdminBundle\Cache\CacheHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdMenuBuilder
{
    /**
     * @var MenuLoaderFactory
     */
    private $menuLoaderFactory;

    /**
     * @var CacheHandler
     */
    private $cacheHandler;

    /**
     * @var string
     */
    private $menuLoader;

    /**
     * Used by Yaml menu loader.
     *
     * @var string
     */
    private $ymlPath;

    /**
     * @param MenuLoaderFactory $menuLoaderFactory
     * @param CacheHandler      $cacheHandler
     */
    public function __construct(MenuLoaderFactory $menuLoaderFactory, CacheHandler $cacheHandler)
    {
        $this->menuLoaderFactory = $menuLoaderFactory;
        $this->cacheHandler = $cacheHandler;
    }

    /**
     * @param string $menuLoader
     */
    public function setMenuLoader($menuLoader)
    {
        $this->menuLoader = $menuLoader;
    }

    /**
     * @param string $ymlPath
     */
    public function setYmlPath($ymlPath)
    {
        $this->ymlPath = $ymlPath;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu()
    {
        $reflectionClass = new \ReflectionClass(self::class);
        if ($this->cacheHandler->hasCache($reflectionClass)) {
            return require $this->cacheHandler->loadCache($reflectionClass);
        } else {
            $menu = $this->menuLoaderFactory->getMenuItems($this->menuLoader, $this->ymlPath);
            $this->cacheHandler->writeCache($reflectionClass, $menu);

            return $menu;
        }
    }
}
