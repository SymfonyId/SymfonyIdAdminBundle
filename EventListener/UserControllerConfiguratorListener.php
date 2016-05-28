<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Controller\UserController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserControllerConfiguratorListener implements CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var string
     */
    private $formClass;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var array
     */
    private $showFields = array();

    /**
     * @var array
     */
    private $gridColumns = array();

    /**
     * @var array
     */
    private $gridFilters = array();

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param array $showFields
     */
    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @param array $gridColumns
     */
    public function setGridColumns(array $gridColumns)
    {
        $this->gridColumns = $gridColumns;
    }

    /**
     * @param array $gridFilters
     */
    public function setGridFilters(array $gridFilters)
    {
        $this->gridFilters = $gridFilters;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->isProduction()) {
            return;
        }

        if (!$this->isValidCrudListener($event)) {
            return;
        }

        if (!$this->controller instanceof UserController) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $this->modelClass ?: $crudConfiguration->getModelClass(),
            'form' => $this->formClass ?: $crudConfiguration->getForm(),
            'menu' => $crudConfiguration->getMenu(),
            'showFields' => empty($this->showFields) ? $crudConfiguration->getShowFields() : $this->showFields,
            'template' => $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $gridConfiguration = $gridConfigurator->getGrid();
        $grid = new Grid(array(
            'column' => empty($this->gridColumns) ? $gridConfiguration->getColumn() : $this->gridColumns,
            'filter' => empty($this->gridFilters) ? $gridConfiguration->getColumn() : $this->gridFilters,
            'sort' => empty($this->gridColumns) ? $gridConfiguration->getColumn() : $this->gridColumns,
        ));
        $gridConfigurator->setGrid($grid);
    }
}
