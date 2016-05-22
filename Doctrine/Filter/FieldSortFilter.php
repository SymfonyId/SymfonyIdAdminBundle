<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Doctrine\Filter;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Filter\FieldSortInterface;
use SymfonyId\AdminBundle\Manager\ManagerFactory;
use SymfonyId\AdminBundle\Model\ModelMetadataTrait;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldSortFilter implements FieldSortInterface
{
    use ModelMetadataTrait;

    const DRIVER = Driver::ORM;

    public function __construct(ManagerFactory $managerFactory)
    {
        $this->setManagerFactory($managerFactory);
    }

    /**
     * @param string                     $entityClass
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string                     $sortBy
     */
    public function sort($entityClass, $queryBuilder, $sortBy)
    {
        $classMetadata = $this->getClassMetadata(new Driver(array('value' => self::DRIVER)), $entityClass);
        $metadata = $classMetadata->getFieldMapping($classMetadata->getFieldName($sortBy));
        $queryBuilder->addOrderBy(sprintf('%s.%s', Constants::ENTITY_ALIAS, $metadata['fieldName']));
    }
}
