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

use Symfony\Component\Yaml\Yaml;
use SymfonyId\AdminBundle\Exception\FileNotFoundException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class YamlMenuLoader implements MenuLoaderInterface
{
    /**
     * @var string
     */
    private $ymlPath;

    /**
     * @param string $ymlPath
     */
    public function setYmlPath($ymlPath)
    {
        $this->ymlPath = $ymlPath;
    }

    /**
     * @return array
     */
    public function getMenuItems()
    {
        if (!file_exists($this->ymlPath)) {
            new FileNotFoundException($this->ymlPath);
        }

        $menus = Yaml::parse(file_get_contents($this->ymlPath));

        return $this->parseMenu($menus);
    }

    /**
     * @param array $menus
     *
     * @return array
     */
    private function parseMenu($menus)
    {
        $menuItems = array();
        foreach ($menus as $name => $config) {
            if (array_key_exists('child', $config)) {
                $this->parseMenu($config['child']);
            } else {
                $menuItems[$config['route']] = array(
                    'name' => $name,
                    'icon' => $config['icon'],
                    'extra' => $config['extra'],
                );
            }
        }

        return $menuItems;
    }
}
