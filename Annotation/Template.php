<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Annotation;

use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 * @Attributes({
 *   @Attribute("value",  type = "string"),
 *   @Attribute("create", type = "string"),
 *   @Attribute("bulkCreate", type = "string"),
 *   @Attribute("edit", type = "string"),
 *   @Attribute("show", type = "string"),
 *   @Attribute("list", type = "string"),
 *   @Attribute("ajaxTemplate", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Template
{
    /**
     * @var string
     */
    private $create = Constants::TEMPLATE_CREATE;

    /**
     * @var string
     */
    private $bulkCreate = Constants::TEMPLATE_BULK_CREATE;

    /**
     * @var string
     */
    private $edit = Constants::TEMPLATE_EDIT;

    /**
     * @var string
     */
    private $show = Constants::TEMPLATE_SHOW;

    /**
     * @var string
     */
    private $list = Constants::TEMPLATE_LIST;

    /**
     * Internal use only.
     *
     * @var string
     */
    private $ajaxTemplate = Constants::TEMPLATE_AJAX;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->create = $data['value'];
        }

        if (isset($data['create'])) {
            $this->create = $data['create'];
        }

        if (isset($data['bulkCreate'])) {
            $this->bulkCreate = $data['bulkCreate'];
        }

        if (isset($data['edit'])) {
            $this->edit = $data['edit'];
        }

        if (isset($data['show'])) {
            $this->show = $data['show'];
        }

        if (isset($data['list'])) {
            $this->list = $data['list'];
        }

        if (isset($data['ajaxTemplate'])) {
            $this->ajaxTemplate = $data['ajaxTemplate'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @return string
     */
    public function getBulkCreate()
    {
        return $this->bulkCreate;
    }

    /**
     * @return string
     */
    public function getEdit()
    {
        return $this->edit;
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return string
     */
    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }
}
