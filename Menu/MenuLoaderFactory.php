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

use SymfonyId\AdminBundle\Exception\MenuNotFoundException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MenuLoaderFactory
{
    /**
     * @var MenuLoaderInterface[]
     */
    private $menuLoaders = array();

    /**
     * @var string
     */
    private $menu;

    /**
     * @param string $menu
     */
    public function __construct($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @param string              $serviceId
     * @param MenuLoaderInterface $menuLoader
     */
    public function addMenuLoader($serviceId, MenuLoaderInterface $menuLoader)
    {
        $this->menuLoaders[$serviceId] = $menuLoader;
    }

    /**
     * @return array
     */
    public function getMenuItems()
    {
        if (!in_array($this->menu, $this->menuLoaders)) {
            throw new MenuNotFoundException($this->menu);
        }
        $loader = $this->menuLoaders[$this->menu];

        return $loader->getMenuItems();
    }
}
