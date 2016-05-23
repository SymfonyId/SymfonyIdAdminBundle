<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Event\EventSubscriber;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\Manager\ManagerFactory;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudOperationHandler
{
    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var EventSubscriber
     */
    private $eventSubscriber;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @param ManagerFactory  $managerFactory
     * @param EventSubscriber $eventSubscriber
     */
    public function __construct(ManagerFactory $managerFactory, EventSubscriber $eventSubscriber)
    {
        $this->managerFactory = $managerFactory;
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * @param Driver         $driver
     * @param ModelInterface $model
     *
     * @return bool
     */
    public function save(Driver $driver, ModelInterface $model)
    {
        $this->managerFactory->getManager($driver)->save($model);

        $manager = $this->managerFactory->getManager($driver);
        $preSaveEvent = new FilterModelEvent();
        $preSaveEvent->setModel($model);
        $preSaveEvent->setManager($manager);
        $this->eventSubscriber->subscribe(Constants::PRE_SAVE, $preSaveEvent);

        try {
            $manager->save($preSaveEvent->getModel());

            $postSaveEvent = new FilterModelEvent();
            $postSaveEvent->setManager($manager);
            $postSaveEvent->setModel($model);
            $this->eventSubscriber->subscribe(Constants::POST_SAVE, $postSaveEvent);
        } catch (\Exception $exception) {
            $this->errorMessage = 'message.save_failed';

            return false;
        }

        return true;
    }

    /**
     * @param Driver $driver
     * @param mixed  $id
     *
     * @return ModelInterface|null
     */
    public function find(Driver $driver, $id)
    {
        return $this->managerFactory->getManager($driver)->find($id);
    }

    /**
     * @param Driver $driver
     * @param int    $page
     * @param int    $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginateResult(Driver $driver, $page, $limit)
    {
        return $this->managerFactory->getManager($driver)->paginate($page, $limit);
    }

    /**
     * @param Driver         $driver
     * @param ModelInterface $model
     *
     * @return bool
     */
    public function remove(Driver $driver, ModelInterface $model)
    {
        $manager = $this->managerFactory->getManager($driver);
        $event = new FilterModelEvent();
        $event->setModel($model);
        $event->setManager($manager);
        $this->eventSubscriber->subscribe(Constants::PRE_DELETE, $event);

        try {
            $manager->remove($model);
        } catch (\Exception $exception) {
            $this->errorMessage = 'message.delete_failed';

            return false;
        }

        return true;
    }
}
