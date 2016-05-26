<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Manager;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use SymfonyId\AdminBundle\Model\ModelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface ManagerInterface
{
    /**
     * @param string $modelClass
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
     * @return ModelInterface|null
     */
    public function find($id);

    /**
     * @return ModelInterface[]
     */
    public function findAll();

    /**
     * @return int
     */
    public function count();

    /**
     * @return string
     */
    public function getDriver();

    /**
     * @return ClassMetadata
     */
    public function getClassMetadata();

    /**
     * @param string
     *
     * @return string
     */
    public function getAliasNamespace($alias);
}
