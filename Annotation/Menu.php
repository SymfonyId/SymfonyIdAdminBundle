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
    private $extraCss;

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
    public function getExtraCss()
    {
        return $this->extraCss;
    }
}
