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
 * @Target({"METHOD"})
 * @Attributes({
 *   @Attribute("value", type = "array"),
 *   @Attribute("groups", type = "array"),
 *   @Attribute("checkDepth", type = "integer"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Serialize
{
    /**
     * @var array
     */
    private $groups = array();

    /**
     * @var int
     */
    private $checkDepth = true;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->groups = (array) $data['value'];
        }

        if (isset($data['groups'])) {
            $this->groups = (array) $data['groups'];
        }

        if (isset($data['checkDepth'])) {
            $this->checkDepth = (int) $data['checkDepth'];
        }

        unset($data);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return int
     */
    public function isCheckDepth()
    {
        return $this->checkDepth;
    }
}
