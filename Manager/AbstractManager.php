<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Manager;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyId\AdminBundle\Event\EventSubscriber;
use SymfonyId\AdminBundle\Exception\ModelNotFoundException;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Model\SoftDeleteAwareInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractManager implements ManagerInterface
{
    /**
     * @var ObjectManager
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
     * @param ObjectManager         $manager
     * @param PaginatorInterface    $paginator
     * @param TokenStorageInterface $tokenStorage
     * @param EventSubscriber       $eventSubscriber
     */
    public function __construct(ObjectManager $manager, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, EventSubscriber $eventSubscriber)
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
        if ($model instanceof SoftDeleteAwareInterface) {
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
     * @return ModelInterface|null
     */
    public function find($id)
    {
        if (!$this->modelClass) {
            throw new ModelNotFoundException('setModelClass');
        }

        return $this->manager->getRepository($this->modelClass)->find($id);
    }

    /**
     * @return ModelInterface[]
     */
    public function findAll()
    {
        if (!$this->modelClass) {
            throw new ModelNotFoundException('setModelClass');
        }

        return $this->manager->getRepository($this->modelClass)->findAll();
    }

    /**
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        if (!$this->modelClass) {
            throw new ModelNotFoundException('setModelClass');
        }

        return $this->manager->getClassMetadata($this->modelClass);
    }

    /**
     * @return EventSubscriber
     */
    protected function getEventSubscriber()
    {
        return $this->eventSubscriber;
    }

    /**
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->manager;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return string
     */
    protected function getModelClass()
    {
        return $this->modelClass;
    }
}
