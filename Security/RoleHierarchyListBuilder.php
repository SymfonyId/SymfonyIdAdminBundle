<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Security;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RoleHierarchyListBuilder
{
    /**
     * @param $roleHierarchy
     *
     * @return array
     */
    public static function buildArrayForChoiceType($roleHierarchy)
    {
        $roleList = array();
        foreach ($roleHierarchy as $key => $value) {
            $roleList[$value] = $value;
        }

        return $roleList;
    }
}
