<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface FieldSortInterface
{
    /**
     * @param string                                                         $modelClass
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     * @param string                                                         $sortBy
     */
    public function sort($modelClass, $queryBuilder, $sortBy);

    /**
     * @param string $key
     * @param string $param
     */
    public function setParameter($key, $param);
}
