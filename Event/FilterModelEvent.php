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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use SymfonyId\AdminBundle\Manager\ManagerInterface;
use SymfonyId\AdminBundle\Model\ModelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterModelEvent extends Event
{
    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param ManagerInterface $objectManager
     */
    public function setManager(ManagerInterface $objectManager)
    {
        $this->manager = $objectManager;
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param ModelInterface $model
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
