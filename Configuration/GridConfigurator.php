<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use SymfonyId\AdminBundle\Annotation\Grid;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GridConfigurator implements ConfiguratorInterface
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\Column
     */
    public function getColumn()
    {
        return $this->grid->getColumn();
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\Filter
     */
    public function getFilter()
    {
        return $this->grid->getFilter();
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\Sort
     */
    public function getSort()
    {
        return $this->grid->getSort();
    }
}
