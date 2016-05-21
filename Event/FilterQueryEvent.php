<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ODM\MongoDB\Query\Builder;
use Symfony\Component\EventDispatcher\Event;
use SymfonyId\AdminBundle\Exception\InvalidQueryBuilderObject;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterQueryEvent extends Event
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $alias;

    /**
     * @param QueryBuilder|Builder $queryBuilder
     *
     * @throws InvalidQueryBuilderObject
     */
    public function setQueryBuilder($queryBuilder)
    {
        if (!is_object($queryBuilder)) {
            throw new InvalidQueryBuilderObject('Parameter must be an object of "\Doctrine\ORM\QueryBuilder" or "Doctrine\ODM\MongoDB\Query\Builder"');
        }

        if (!$queryBuilder instanceof QueryBuilder && !$queryBuilder instanceof Builder) {
            throw new InvalidQueryBuilderObject(sprintf('%s is not valid query builder object', get_class($queryBuilder)));
        }

        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
