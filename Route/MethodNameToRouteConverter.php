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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SymfonyId\AdminBundle\Exception\MethodNotValidException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodNameToRouteConverter
{
    /**
     * @param string $methodName
     * @param bool   $flag
     *
     * @return Route
     */
    public static function convert($methodName, $flag = false)
    {
        if (!in_array($methodName, SymfonyIdRouteLoader::$VALID_CRUD_METHODS)) {
            throw new MethodNotValidException($methodName);
        }

        switch ($methodName) {
            case SymfonyIdRouteLoader::METHOD_NEW :
            case SymfonyIdRouteLoader::METHOD_DOWNLOAD:
            case SymfonyIdRouteLoader::METHOD_LIST:
                if (!$flag) {
                    return new Route(array(
                        'path' => '/'.$methodName.'/',
                    ));
                } else {
                    return new Route(array(
                        'path' => '/',
                    ));
                }
                break;
            case SymfonyIdRouteLoader::METHOD_EDIT:
            case SymfonyIdRouteLoader::METHOD_SHOW:
            case SymfonyIdRouteLoader::METHOD_DELETE:
                return new Route(array(
                    'path' => '/{id}/'.$methodName.'/',
                ));
                break;
            case SymfonyIdRouteLoader::METHOD_BULK_DELETE:
                return new Route(array(
                    'path' => '/bulk-delete/',
                ));
                break;
            case SymfonyIdRouteLoader::METHOD_BULK_NEW:
                return new Route(array(
                    'path' => '/bulk-new/',
                ));
                break;
        }

        return new Route(array('path' => '/'));
    }
}
