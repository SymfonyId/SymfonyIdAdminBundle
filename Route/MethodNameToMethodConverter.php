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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SymfonyId\AdminBundle\Exception\MethodNotValidException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodNameToMethodConverter
{
    /**
     * @param string $methodName
     *
     * @return Method
     */
    public static function convert($methodName)
    {
        if (!in_array($methodName, SymfonyIdRouteLoader::$VALID_CRUD_METHODS)) {
            throw new MethodNotValidException($methodName);
        }

        switch ($methodName) {
            case SymfonyIdRouteLoader::METHOD_NEW :
            case SymfonyIdRouteLoader::METHOD_EDIT:
                return new Method(array(
                    'methods' => array('GET', 'POST'),
                ));
                break;
            case SymfonyIdRouteLoader::METHOD_SHOW:
            case SymfonyIdRouteLoader::METHOD_DOWNLOAD:
            case SymfonyIdRouteLoader::METHOD_LIST:
                return new Method(array(
                    'methods' => array('GET'),
                ));
                break;
            case SymfonyIdRouteLoader::METHOD_BULK_DELETE:
            case SymfonyIdRouteLoader::METHOD_DELETE:
                return new Method(array(
                    'methods' => array('DELETE'),
                ));
                break;
            case SymfonyIdRouteLoader::METHOD_BULK_NEW:
                return new Method(array(
                    'methods' => array('POST'),
                ));
                break;
        }

        return new Method(array(
            'methods' => array(),
        ));
    }
}
