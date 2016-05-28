<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Document;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Event\EventSubscriber;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Exception\ModelNotFoundException;
use SymfonyId\AdminBundle\Manager\AbstractManager;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DoctrineMongoDbManager extends AbstractManager
{
    /**
     * @param ManagerRegistry       $managerRegistry
     * @param DocumentManager       $manager
     * @param PaginatorInterface    $paginator
     * @param TokenStorageInterface $tokenStorage
     * @param EventSubscriber       $eventSubscriber
     */
    public function __construct(ManagerRegistry $managerRegistry, DocumentManager $manager, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, EventSubscriber $eventSubscriber)
    {
        parent::__construct($managerRegistry, $manager, $paginator, $tokenStorage, $eventSubscriber);
    }

    /**
     * @param $page
     * @param $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate($page, $limit)
    {
        if (!$this->getModelClass()) {
            throw new ModelNotFoundException('setModelClass');
        }

        /** @var DocumentManager $manager */
        $manager = $this->getManager();
        $queryBuilder = $manager->createQueryBuilder($this->getModelClass());
        $filterList = new FilterQueryEvent();
        $filterList->setQueryBuilder($queryBuilder);
        $filterList->setModelClass($this->getModelClass());
        $this->getEventSubscriber()->subscribe(Constants::FILTER_LIST, $filterList);

        $query = $queryBuilder->getQuery();

        return $this->getPaginator()->paginate($query, $page, $limit);
    }

    /**
     * @return int
     */
    public function count()
    {
        /** @var DocumentManager $manager */
        $manager = $this->getManager();
        $queryBuilder = $manager->createQueryBuilder($this->getModelClass());
        $queryBuilder->eagerCursor(true);
        $queryBuilder->prime(true);

        return $queryBuilder->getQuery()->execute()->count();
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return Driver::ODM;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Query\FilterCollection
     */
    public function getFilters()
    {
        return $this->getManager()->getFilterCollection();
    }
}
