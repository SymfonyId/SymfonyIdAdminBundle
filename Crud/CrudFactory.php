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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var CrudOperationHandler
     */
    private $crudOperationHandler;

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
    private $modelClass;

    /**
     * @var string
     */
    private $template;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param EngineInterface      $templateEngine
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, EngineInterface $templateEngine)
    {
        $this->crudOperationHandler = $crudOperationHandler;
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
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
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
}
