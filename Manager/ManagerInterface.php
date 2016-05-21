<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Manager;

use SymfonyId\AdminBundle\Model\ModelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface ManagerInterface
{
    /**
     * @param string $modelClass\
     */
    public function setModelClass($modelClass);

    /**
     * @param $page
     * @param $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate($page, $limit);

    /**
     * @param ModelInterface $model
     */
    public function save(ModelInterface $model);

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model);

    /**
     * @param mixed $id
     *
     * @return ModelInterface
     */
    public function find($id);

    /**
     * @return string
     */
    public function getDriver();
}
