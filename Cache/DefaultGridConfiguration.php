<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use SymfonyId\AdminBundle\Annotation\Column;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultGridConfiguration implements DefaultConfigurationInterface
{
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
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfiguration(ConfiguratorFactory $configuratorFactory)
    {
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $gridConfiguration = $gridConfigurator->getGrid();
        $grid = new Grid(array(
            'column' => $gridConfiguration->getColumn(),
            'filter' => empty($this->gridFilters) ? $gridConfiguration->getColumn() : new Column(array('value' => $this->gridFilters)),
            'sort' => $gridConfiguration->getColumn(),
        ));
        $gridConfigurator->setGrid($grid);
    }
}
