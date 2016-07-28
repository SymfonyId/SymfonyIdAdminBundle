<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Extractor;

use SymfonyId\AdminBundle\Exception\FreezeStateException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Extractor
{
    const CLASS_ANNOTATION = 0;
    const METHOD_ANNOTATAION = 1;
    const PROPERTY_ANNOTATION = 2;

    /**
     * @var \Reflector
     */
    private $object;

    /**
     * @var array
     */
    private $extractors = array();

    /**
     * @var bool
     */
    private $freeze = false;

    /**
     * @param ExtractorInterface $extractor
     *
     * @throws FreezeStateException
     */
    public function addExtractor(ExtractorInterface $extractor)
    {
        if ($this->freeze) {
            throw new FreezeStateException($this);
        }

        $this->extractors[get_class($extractor)] = $extractor;
    }

    /**
     * @param \Reflector $reflector
     * @param int        $type
     *
     * @return array
     */
    public function extract(\Reflector $reflector, $type = self::CLASS_ANNOTATION)
    {
        $this->object = $reflector;

        switch ($type) {
            case self::CLASS_ANNOTATION:
                return $this->getClassAnnotations();
            break;
            case self::METHOD_ANNOTATAION:
                return $this->getMethodAnnotations();
            break;
            case self::PROPERTY_ANNOTATION:
                return $this->getPropertyAnnotations();
            break;
        }

        return array();
    }

    /**
     * @return array
     */
    private function getClassAnnotations()
    {
        $annotations = array();

        /** @var ClassExtractor $extractor */
        $extractor = $this->getExtractor(ClassExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    /**
     * @return array
     */
    private function getMethodAnnotations()
    {
        $annotations = array();

        /** @var MethodExtractor $extractor */
        $extractor = $this->getExtractor(MethodExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            foreach ($this->object->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionMethod));
            }
        }

        if ($this->object instanceof \ReflectionMethod) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    /**
     * @return array
     */
    private function getPropertyAnnotations()
    {
        $annotations = array();

        /** @var PropertyExtractor $extractor */
        $extractor = $this->getExtractor(PropertyExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            foreach ($this->object->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionProperty));
            }
        }

        if ($this->object instanceof \ReflectionProperty) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    public function freeze()
    {
        $this->freeze = true;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private function getExtractor($name)
    {
        return $this->extractors[$name];
    }
}
