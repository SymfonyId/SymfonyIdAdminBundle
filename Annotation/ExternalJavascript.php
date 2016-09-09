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
 *   @Attribute("includFiles", type = "array"),
 *   @Attribute("includeRoutes", type = "array"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class ExternalJavascript
{
    /**
     * @var array
     */
    private $includFiles = array();

    /**
     * @var array
     */
    private $includeRoutes = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['includFiles'])) {
            $this->includFiles = $data['includFiles'];
        }

        if (isset($data['includeRoutes'])) {
            $this->includeRoutes = $data['includeRoutes'];
        }

        unset($data);
    }

    /**
     * @return array
     */
    public function getIncludFiles()
    {
        return $this->includFiles;
    }

    /**
     * @return array
     */
    public function getIncludeRoutes()
    {
        return $this->includeRoutes;
    }
}
