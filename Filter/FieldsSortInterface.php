<?php

/*
 * This file is part of the AdminBundle package.
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
interface FieldsSortInterface
{
    /**
     * @param string $entityClass
     * @param \Doctrine\ORM\QueryBuilder|Doctrine\ODM\MongoDB\Query\Builder
     * @param string
     */
    public function sort($entityClass, $queryBuilder, $sortBy);
}
