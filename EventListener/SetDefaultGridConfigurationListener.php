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
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SetDefaultGridConfigurationListener implements CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var array
     */
    private $gridFilters = array();

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
}
