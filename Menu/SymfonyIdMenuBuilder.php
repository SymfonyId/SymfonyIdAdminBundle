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
     */
    public function __construct(MenuLoaderFactory $menuLoaderFactory)
    {
        $this->menuLoaderFactory = $menuLoaderFactory;
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
        return $this->menuLoaderFactory->getMenu($this->menuLoader, $this->ymlPath);
    }
}
