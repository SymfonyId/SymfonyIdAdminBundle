<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RequestParameter
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $filter;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @param Request $request
     * @param int $limit
     * @param int $page
     */
    public function __construct(Request $request, $limit = 17, $page = 1)
    {
        $this->limit = $request->query->get('limit', $limit);
        $this->page = $request->query->get('page', $page);
        $this->filter = $request->query->get('filter');
        $this->sortBy = $request->query->get('sory_by');
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'page' => $this->page,
            'limit' => $this->limit,
            'sort_by' => $this->sortBy,
            'filter' => $this->filter,
        );
    }
}
