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

use SymfonyId\AdminBundle\Exception\MenuLoaderNotFoundException;

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
     * @param string              $serviceId
     * @param MenuLoaderInterface $menuLoader
     */
    public function addMenuLoader($serviceId, MenuLoaderInterface $menuLoader)
    {
        $this->menuLoaders[$serviceId] = $menuLoader;
    }

    /**
     * @param string $menu
     * @param string $ymlPath
     *
     * @return array
     */
    public function getMenuItems($menu, $ymlPath = null)
    {
        if (!in_array($menu, array_keys($this->menuLoaders))) {
            throw new MenuLoaderNotFoundException($menu);
        }
        $loader = $this->menuLoaders[$menu];
        if ($loader instanceof YamlMenuLoader) {
            $loader->setYmlPath($ymlPath);
        }

        return $loader->getMenuItems();
    }
}
