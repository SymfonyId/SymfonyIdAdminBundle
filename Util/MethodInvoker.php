<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Util;

use SymfonyId\AdminBundle\Exception\RuntimeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodInvoker
{
    /**
     * @param mixed  $object   Object
     * @param string $property name of property that want to invoke
     *
     * @return mixed return value of method that invoked
     *
     * @throws RuntimeException
     */
    public static function invokeGet($object, $property)
    {
        $method = CamelCaser::underScoresToCamelCase($property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCaser::underScoresToCamelCase('get_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCaser::underScoresToCamelCase('is_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        throw new RuntimeException(sprintf('Property "%s" does not has any getter method.', $property));
    }

    /**
     * @param array $data   with key => value
     * @param mixed $object Object that you want to bind
     *
     * @return mixed $object Object
     *
     * @throws RuntimeException
     */
    public static function bindSet(array $data, $object)
    {
        if (!is_object($object)) {
            throw new RuntimeException(sprintf('"%s" is not object.', $object));
        }

        foreach ($data as $key => $value) {
            $method = CamelCaser::underScoresToCamelCase(sprintf('set_%s', $key));

            if (method_exists($object, $method)) {
                call_user_func_array(array($object, $method), array($value));
            } else {
                $method = CamelCaser::underScoresToCamelCase($key);

                if (!method_exists($object, $method)) {
                    $method = CamelCaser::underScoresToCamelCase(sprintf('is_%s', $key));
                }

                call_user_func_array(array($object, $method), array($value));
            }
        }

        return $object;
    }
}
