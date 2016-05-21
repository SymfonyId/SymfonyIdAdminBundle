<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Event\EventSubscriber;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Exception\ModelNotFoundException;
use SymfonyId\AdminBundle\Manager\ManagerInterface;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Model\SoftDeletableInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class OrmManager implements ManagerInterface
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EventSubscriber
     */
    private $eventSubscriber;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @param EntityManager         $manager
     * @param PaginatorInterface    $paginator
     * @param TokenStorageInterface $tokenStorage
     * @param EventSubscriber       $eventSubscriber
     */
    public function __construct(EntityManager $manager, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, EventSubscriber $eventSubscriber)
    {
        $this->manager = $manager;
        $this->paginator = $paginator;
        $this->tokenStorage = $tokenStorage;
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param $page
     * @param $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate($page, $limit)
    {
        if (!$this->modelClass) {
            throw new ModelNotFoundException('setModelClass');
        }

        $queryBuilder = $this->manager->getRepository($this->modelClass)->createQueryBuilder(Constants::ENTITY_ALIAS);
        $filterList = new FilterQueryEvent();
        $filterList->setQueryBuilder($queryBuilder);
        $filterList->setAlias(Constants::ENTITY_ALIAS);
        $filterList->setEntityClass($this->modelClass);
        $this->eventSubscriber->subscribe(Constants::FILTER_LIST, $filterList);

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query, $page, $limit);
    }

    /**
     * @param ModelInterface $model
     */
    public function save(ModelInterface $model)
    {
        $this->manager->persist($model);
        $this->manager->flush();
    }

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model)
    {
        if ($model instanceof SoftDeletableInterface) {
            $model->isDeleted(true);
            $model->setDeletedAt(new \DateTime());
            $model->setDeletedBy($this->tokenStorage->getToken()->getUsername());

            $this->save($model);
        } else {
            $this->manager->remove($model);
            $this->manager->flush();
        }
    }

    /**
     * @param mixed $id
     *
     * @return ModelInterface
     */
    public function find($id)
    {
        if (!$this->modelClass) {
            throw new ModelNotFoundException('setModelClass');
        }

        return $this->manager->getRepository($this->modelClass)->find($id);
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return Driver::ORM;
    }
}
