<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Crud\ActionHandler\ActionHandlerInterface;
use SymfonyId\AdminBundle\Crud\ActionHandler\BulkCreateActionHandler;
use SymfonyId\AdminBundle\Crud\ActionHandler\BulkDeleteActionHandler;
use SymfonyId\AdminBundle\Crud\ActionHandler\CreateUpdateActionHandler;
use SymfonyId\AdminBundle\Crud\ActionHandler\DeleteActionHandler;
use SymfonyId\AdminBundle\Crud\ActionHandler\DetailActionHandler;
use SymfonyId\AdminBundle\Crud\ActionHandler\ListActionHandler;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudFactory
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var array
     */
    private $actionHandlers = array();

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var View
     */
    private $view;

    /**
     * @var string
     */
    private $template;

    /**
     * @param EngineInterface $templateEngine
     */
    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param ActionHandlerInterface $viewHandler
     */
    public function addActionHandler(ActionHandlerInterface $viewHandler)
    {
        $this->actionHandlers[get_class($viewHandler)] = $viewHandler;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Driver $driver
     */
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param CrudConfigurator $crudConfigurator
     * @param array            $gridFields
     * @param bool             $allowBulkDelete
     * @param bool             $formatNumber
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listView(CrudConfigurator $crudConfigurator, array $gridFields, $allowBulkDelete, $formatNumber)
    {
        /** @var ListActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[ListActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);
        $viewHandler->setGridFields($gridFields);
        $viewHandler->setActionList($crudConfigurator->getGridAction());
        $viewHandler->isAllowCrate($crudConfigurator->getCrud()->isAllowCreate());
        $viewHandler->isAllowBulkDelete($allowBulkDelete);
        $viewHandler->isFormatNumber($formatNumber);

        return $this->getResponse($viewHandler->getView($this->driver));
    }

    /**
     * @param FormInterface $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrUpdate(FormInterface $form)
    {
        /** @var CreateUpdateActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[CreateUpdateActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);
        $viewHandler->setForm($form);

        return $this->getResponse($viewHandler->getView($this->driver));
    }

    /**
     * @param ModelInterface $model
     * @param array          $showFields
     * @param bool           $allowDelete
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail(ModelInterface $model, array $showFields, $allowDelete = true)
    {
        /** @var DetailActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[DetailActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);
        $viewHandler->setData($model);
        $viewHandler->setShowFields($showFields);
        $viewHandler->isDelete($allowDelete);

        return $this->getResponse($viewHandler->getView($this->driver));
    }

    /**
     * @param ModelInterface $model
     *
     * @return JsonResponse
     *
     * @throws \SymfonyId\AdminBundle\Exception\RuntimeException
     */
    public function remove(ModelInterface $model)
    {
        /** @var DeleteActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[DeleteActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);
        $viewHandler->setData($model);

        $view = $viewHandler->getView($this->driver);

        return new JsonResponse(array(
            'status' => $view->getParam('errors'),
            'message' => $view->getParam('message'),
        ));
    }

    /**
     * @param CrudConfigurator $crudConfigurator
     *
     * @return JsonResponse
     *
     * @throws \SymfonyId\AdminBundle\Exception\RuntimeException
     */
    public function bulkCreate(CrudConfigurator $crudConfigurator)
    {
        /** @var BulkCreateActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[BulkCreateActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);
        $viewHandler->setCrudConfigurator($crudConfigurator);

        $view = $viewHandler->getView($this->driver);

        return new JsonResponse(array(
            'status' => $view->getParam('status'),
            'message' => $view->getParam('message'),
        ));
    }

    /**
     * @return JsonResponse
     *
     * @throws \SymfonyId\AdminBundle\Exception\RuntimeException
     */
    public function bulkDelete()
    {
        /** @var BulkDeleteActionHandler $viewHandler */
        $viewHandler = $this->actionHandlers[BulkDeleteActionHandler::class];
        $viewHandler->setView($this->view);
        $viewHandler->setRequest($this->request);

        $view = $viewHandler->getView($this->driver);

        return new JsonResponse(array(
            'status' => $view->getParam('status'),
            'message' => $view->getParam('message'),
        ));
    }

    /**
     * @param View $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getResponse(View $view)
    {
        return $this->templateEngine->renderResponse($this->template, $view->getParams());
    }
}
