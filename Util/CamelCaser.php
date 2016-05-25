<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Util;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CamelCaser
{
    /**
     * @param $string
     *
     * @return string
     */
    public static function underScoresToCamelCase($string)
    {
        return preg_replace_callback('/_([a-z])/', function ($match) {
            return strtoupper($match[1]);
        }, strtolower($string));
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function camelCaseToUnderScore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}
