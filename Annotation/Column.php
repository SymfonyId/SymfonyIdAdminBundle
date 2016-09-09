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
 * @Target({"ANNOTATION", "PROPERTY"})
 * @Attributes({
 *   @Attribute("value", type = "array"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Column
{
    /**
     * @var array
     */
    private $value = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->value = $data['value'];
        }

        unset($data);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->value;
    }
}
