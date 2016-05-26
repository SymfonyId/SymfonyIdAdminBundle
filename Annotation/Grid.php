<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("value", type = "\SymfonyId\AdminBundle\Annotation\Column"),
 *   @Attribute("column", type = "\SymfonyId\AdminBundle\Annotation\Column"),
 *   @Attribute("filter", type = "\SymfonyId\AdminBundle\Annotation\Filter"),
 *   @Attribute("sort", type = "\SymfonyId\AdminBundle\Annotation\Sort"),
 *   @Attribute("normalizeFilter",  type = "boolean"),
 *   @Attribute("formatNumber",  type = "boolean"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Grid
{
    /**
     * @var Column
     */
    private $column;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Sort
     */
    private $sort;

    /**
     * @var bool
     */
    private $normalizeFilter = false;

    /**
     * @var bool
     */
    private $formatNumber = true;

    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->column = $data['value'];
        }

        if (isset($data['column'])) {
            $this->column = $data['column'];
        }

        if (isset($data['filter'])) {
            $this->filter = $data['filter'];
        }

        if (isset($data['sort'])) {
            $this->sort = $data['sort'];
        }

        if (isset($data['normalizeFilter'])) {
            $this->normalizeFilter = $data['normalizeFilter'];
        }

        if (isset($data['formatNumber'])) {
            $this->formatNumber = $data['formatNumber'];
        }

        unset($data);
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return Sort
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return bool
     */
    public function isNormalizeFilter()
    {
        return $this->normalizeFilter;
    }

    /**
     * @return bool
     */
    public function isFormatNumber()
    {
        return $this->formatNumber;
    }
}
