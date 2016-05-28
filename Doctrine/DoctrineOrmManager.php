<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
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
class DoctrineOrmManager extends AbstractManager
{
    /**
     * @param ManagerRegistry       $managerRegistry
     * @param EntityManager         $manager
     * @param PaginatorInterface    $paginator
     * @param TokenStorageInterface $tokenStorage
     * @param EventSubscriber       $eventSubscriber
     */
    public function __construct(ManagerRegistry $managerRegistry, EntityManager $manager, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, EventSubscriber $eventSubscriber)
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

        /** @var EntityRepository $repository */
        $repository = $this->getManager()->getRepository($this->getModelClass());
        $queryBuilder = $repository->createQueryBuilder(Constants::MODEL_ALIAS);
        $filterList = new FilterQueryEvent();
        $filterList->setQueryBuilder($queryBuilder);
        $filterList->setAlias(Constants::MODEL_ALIAS);
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
        /** @var EntityRepository $repository */
        $repository = $this->getManager()->getRepository($this->getModelClass());
        $queryBuilder = $repository->createQueryBuilder(Constants::MODEL_ALIAS);
        $queryBuilder->select(sprintf('COUNT(%s.id)', Constants::MODEL_ALIAS));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return Driver::ORM;
    }

    /**
     * @return \Doctrine\ORM\Query\FilterCollection
     */
    public function getFilters()
    {
        return $this->getManager()->getFilters();
    }
}
