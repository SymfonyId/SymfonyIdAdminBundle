<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Route;

use Symfony\Component\Routing\RouteCollection;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UniqueRouteNameGenerator
{
    /**
     * @param RouteCollection $routeCollection
     * @param string          $routeName
     *
     * @return string
     */
    public static function generate(RouteCollection $routeCollection, $routeName)
    {
        $flag = false;
        $index = 1;
        while ($flag === false) {
            if ($routeCollection->get($routeName)) {
                $routeName = $routeName.'_'.$index++;
            } else {
                $flag = true;
            }
        }

        return $routeName;
    }
}
