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
 *   @Attribute("value",  type = "string"),
 *   @Attribute("template", type = "\SymfonyId\AdminBundle\Annotation\Template"),
 *   @Attribute("showFields",  type = "array"),
 *   @Attribute("modelClass",  type = "string"),
 *   @Attribute("form",  type = "string"),
 *   @Attribute("menu",  type = "\SymfonyId\AdminBundle\Annotation\Menu"),
 *   @Attribute("allowCreate",  type = "boolean"),
 *   @Attribute("allowEdit",  type = "boolean"),
 *   @Attribute("allowShow",  type = "boolean"),
 *   @Attribute("allowDelete",  type = "boolean"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Crud
{
    /**
     * @var Template
     */
    private $template;

    /**
     * Entity fields you want to display.
     *
     * Ex: @Crud(showFields={"first_name", "last_name"})
     *
     * @var array
     */
    private $showFields = array();

    /**
     * Ex: @Crud(modelClass="AppBundle/Entity/Product").
     *
     * @var string
     */
    private $modelClass;

    /**
     * Ex: @Crud(form="AppBundle/Form/ProductType").
     *
     * @var string
     */
    private $form;

    /**
     * @var Menu
     */
    private $menu;

    /**
     * @var bool
     */
    private $allowCreate = true;

    /**
     * @var bool
     */
    private $allowEdit = true;

    /**
     * @var bool
     */
    private $allowShow = true;

    /**
     * @var bool
     */
    private $allowDelete = true;

    /**
     * @var bool
     */
    private $allowDownload = true;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->modelClass = $data['value'];
        }

        if (isset($data['modelClass'])) {
            $this->modelClass = $data['modelClass'];
        }

        if (isset($data['form'])) {
            $this->form = $data['form'];
        }

        if (isset($data['menu'])) {
            $this->menu = $data['menu'];
        }

        if (isset($data['showFields'])) {
            $this->showFields = (array) $data['showFields'];
        }

        if (isset($data['template'])) {
            $this->template = $data['template'];
        }

        if (isset($data['allowCreate'])) {
            $this->allowCreate = (bool) $data['allowCreate'];
        }

        if (isset($data['allowEdit'])) {
            $this->allowEdit = (bool) $data['allowEdit'];
        }

        if (isset($data['allowShow'])) {
            $this->allowShow = (bool) $data['allowShow'];
        }

        if (isset($data['allowDelete'])) {
            $this->allowDelete = (bool) $data['allowDelete'];
        }

        if (isset($data['allowDownload'])) {
            $this->allowDownload = (bool) $data['allowDownload'];
        }

        unset($data);
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return array
     */
    public function getShowFields()
    {
        return $this->showFields;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return bool
     */
    public function isAllowCreate()
    {
        return $this->allowCreate;
    }

    /**
     * @return bool
     */
    public function isAllowEdit()
    {
        return $this->allowEdit;
    }

    /**
     * @return bool
     */
    public function isAllowShow()
    {
        return $this->allowShow;
    }

    /**
     * @return bool
     */
    public function isAllowDelete()
    {
        return $this->allowDelete;
    }

    /**
     * @return bool
     */
    public function isAllowDownload()
    {
        return $this->allowDownload;
    }
}
