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
 *   @Attribute("uploadable", type = "string"),
 *   @Attribute("targetField", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Upload
{
    /**
     * - Model field to be uploadable
     * - Must has setter getter method.
     *
     * @var string
     */
    private $uploadable;

    /**
     * - Model field to store file path
     * - Must has setter getter method.
     *
     * @var string
     */
    private $targetField;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['uploadable'])) {
            $this->uploadable = $data['uploadable'];
        }

        if (isset($data['targetField'])) {
            $this->targetField = $data['targetField'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getUploadable()
    {
        return $this->uploadable;
    }

    /**
     * @return string
     */
    public function getTargetField()
    {
        return $this->targetField;
    }
}
