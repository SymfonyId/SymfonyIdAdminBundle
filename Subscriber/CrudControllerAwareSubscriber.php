<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfigurationMapper;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareInterface;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareTrait;
use SymfonyId\AdminBundle\Controller\UserController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudControllerAwareSubscriber implements CrudControllerEventAwareInterface, EventSubscriberInterface
{
    use CrudControllerEventAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var ConfigurationMapper
     */
    private $configurationMapper;

    /**
     * @var array
     */
    private $gridFilters = array();

    /**
     * @param KernelInterface     $kernel
     * @param ConfigurationMapper $configurationMapper
     */
    public function __construct(KernelInterface $kernel, ConfigurationMapper $configurationMapper)
    {
        $this->kernel = $kernel;
        $this->configurationMapper = $configurationMapper;
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
     *
     * @throws \Exception
     */
    public function extractUserController(FilterControllerEvent $event)
    {
        if ($this->isProduction()) {
            return;
        }

        if (!$this->isValidCrudListener($event)) {
            return;
        }

        if ($this->controller instanceof UserController) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        $this->configurationMapper->map($configuratorFactory, $reflectionController);
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @return bool
     */
    public function setControllerDependencies(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];

        if ($controller instanceof ConfigurationAwareInterface) {
            $controller->setKernel($this->container->get('kernel'));
            $controller->setCacheHandler($this->container->get('symfonyid.admin.cache.cache_handler'));
            $controller->setConfiguratorFactory($this->container->get('symfonyid.admin.configuration.configurator_factory'));
        }
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function setDefaultCrudConfiguration(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $crudConfiguration->getModelClass(),
            'form' => $crudConfiguration->getForm(),
            'menu' => $crudConfiguration->getMenu(),
            'showFields' => $crudConfiguration->getShowFields(),
            'template' => $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function setDefaultGridConfiguration(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $gridConfiguration = $gridConfigurator->getGrid();
        $grid = new Grid(array(
            'column' => $gridConfiguration->getColumn(),
            'filter' => empty($this->gridFilters) ? $gridConfiguration->getFilter() : new Filter(array('value' => $this->gridFilters)),
            'sort' => $gridConfiguration->getColumn(),
        ));
        $gridConfigurator->setGrid($grid);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('extractUserController', 127),
                array('setControllerDependencies', 0),
                array('setDefaultCrudConfiguration', 255),
                array('setDefaultCrudConfiguration', 255),
            ),
        );
    }
}
