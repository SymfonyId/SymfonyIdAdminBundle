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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\Driver;
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
    private $viewHandlers = array();

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Driver
     */
    private $driver;

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
     * @param ViewHandlerInterface $viewHandler
     */
    public function addViewHandler(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandlers[get_class($viewHandler)] = $viewHandler;
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
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param View $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(View $view)
    {
        return $this->templateEngine->renderResponse($this->template, $view->getParams());
    }

    /**
     * @param array $gridFields
     * @param array $actionList
     * @param bool  $allowCreate
     * @param bool  $allowBulkDelete
     * @param bool  $formatNumber
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listView(array $gridFields, array $actionList, $allowCreate = true, $allowBulkDelete = true, $formatNumber = true)
    {
        /** @var ListActionHandler $viewHandler */
        $viewHandler = $this->viewHandlers[ListActionHandler::class];
        $viewHandler->setRequest($this->request);
        $viewHandler->setGridFields($gridFields);
        $viewHandler->setActionList($actionList);
        $viewHandler->isAllowCrate($allowCreate);
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
        $viewHandler = $this->viewHandlers[CreateUpdateActionHandler::class];
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
        $viewHandler = $this->viewHandlers[DetailActionHandler::class];
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
        $viewHandler = $this->viewHandlers[DeleteActionHandler::class];
        $viewHandler->setRequest($this->request);
        $viewHandler->setData($model);

        $view = $viewHandler->getView($this->driver);

        return new JsonResponse(array(
            'status' => $view->getParam('errors'),
            'message' => $view->getParam('message'),
        ));
    }
}
