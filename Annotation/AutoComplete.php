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

/**
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 * @Attributes({
 *   @Attribute("routeResource", type = "string"),
 *   @Attribute("routeCallback", type = "string"),
 *   @Attribute("targetSelector", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AutoComplete
{
    /**
     * Route to search action.
     *
     * @var string
     */
    private $routeResource;

    /**
     * Route to get id action.
     *
     * @var string
     */
    private $routeCallback;

    /**
     * jQuery selector to store value.
     *
     * @var string
     */
    private $targetSelector;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['routeResource'])) {
            $this->routeResource = $data['routeResource'];
        }

        if (isset($data['routeCallback'])) {
            $this->routeCallback = $data['routeCallback'];
        }

        if (isset($data['targetSelector'])) {
            $this->targetSelector = $data['targetSelector'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getRouteResource()
    {
        return $this->routeResource;
    }

    /**
     * @return string
     */
    public function getRouteCallback()
    {
        return $this->routeCallback;
    }

    /**
     * @return string
     */
    public function getTargetSelector()
    {
        return $this->targetSelector;
    }
}
