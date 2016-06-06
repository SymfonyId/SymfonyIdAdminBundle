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
interface MenuLoaderInterface
{
    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu();

    /**
     * @param bool $include
     *
     * @return bool
     */
    public function isIncludeDefault($include = true);
}
