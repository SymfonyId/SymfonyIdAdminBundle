<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Model;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Manager\ManagerFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait ModelMetadataAwareTrait
{
    /**
     * @var ManagerFactory
     */
    protected $managerFactory;

    /**
     * @param ManagerFactory $managerFactory
     */
    public function setManagerFactory(ManagerFactory $managerFactory)
    {
        $this->managerFactory = $managerFactory;
    }

    /**
     * @param Driver $driver
     * @param string $modelClass
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata(Driver $driver, $modelClass)
    {
        $manager = $this->managerFactory->getManager($driver);
        $manager->setModelClass($modelClass);

        return $manager->getClassMetadata();
    }

    /**
     * @param Driver $driver
     * @param string $alias
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getAliasNamespace(Driver $driver, $alias)
    {
        $manager = $this->managerFactory->getManager($driver);

        return $manager->getAliasNamespace($alias);
    }
}
