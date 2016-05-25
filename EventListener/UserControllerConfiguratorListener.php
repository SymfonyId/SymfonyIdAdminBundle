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
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Controller\UserController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserControllerConfiguratorListener implements CrudControllerListenerAwareInterface, ConfigurationAwareInterface
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
     * @param string $modelClass
     */
    public function setForm($formClass, $modelClass)
    {
        $this->formClass = $formClass;
        $this->modelClass = $modelClass;
    }

    /**
     * @param array $showFields
     * @param array $gridColumns
     * @param array $gridFilters
     */
    public function setView(array $showFields, array $gridColumns, array $gridFilters)
    {
        $this->showFields = $showFields;
        $this->gridColumns = $gridColumns;
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
            'modelClass' => $this->modelClass,
            'form' => $this->formClass,
            'menuIcon' => $crudConfiguration->getMenuIcon(),
            'showFields' => $this->showFields,
            'template' => $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $grid = new Grid(array(
            'column' => $this->gridColumns,
            'filter' => $this->gridFilters,
            'sort' => $this->gridColumns,
        ));
        $gridConfigurator->setGrid($grid);
    }
}
