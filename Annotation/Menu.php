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
 * @Target({"ANNOTATION"})
 * @Attributes({
 *   @Attribute("value", type = "string"),
 *   @Attribute("icon", type = "string"),
 *   @Attribute("extraCss", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Menu
{
    /**
     * @var string
     */
    private $icon = 'fa-bars';

    /**
     * @var string
     */
    private $extra;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->icon = $data['value'];
        }

        if (isset($data['icon'])) {
            $this->icon = $data['icon'];
        }

        if (isset($data['extra'])) {
            $this->extra = $data['extra'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
