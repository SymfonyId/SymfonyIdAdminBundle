<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface RecordsHandlerInterface
{
    /**
     * @param PaginationInterface $pagination
     * @param array $fields
     * @return Records
     */
    public function process(PaginationInterface $pagination, array $fields);
}
