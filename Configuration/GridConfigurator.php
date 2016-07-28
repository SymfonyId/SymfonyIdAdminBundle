<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Annotation\Column;
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Annotation\Sort;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GridConfigurator implements ConfiguratorInterface
{
    /**
     * @var Extractor
     */
    private $extractorFactory;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param Extractor $extractorFactory
     * @param KernelInterface  $kernel
     */
    public function __construct(Extractor $extractorFactory, KernelInterface $kernel)
    {
        $this->extractorFactory = $extractorFactory;
        $this->kernel = $kernel;
    }

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    public function getColumns(\ReflectionClass $reflectionClass)
    {
        if ($this->isProduction()) {
            $this->grid->getColumn()->getFields();
        }

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $property) {
            $propertyAnnotations = $this->extractorFactory->extract($property, Extractor::PROPERTY_ANNOTATION);
            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Column) {
                    $fields[] = $property->getName();
                }
            }
        }

        return !empty($fields) ? $fields : $this->grid->getColumn()->getFields();
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    public function getFilters(\ReflectionClass $reflectionClass)
    {
        if ($this->isProduction()) {
            $this->grid->getFilter()->getFields();
        }

        /** @var \ReflectionProperty $property */
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $property) {
            $propertyAnnotations = $this->extractorFactory->extract($property, Extractor::PROPERTY_ANNOTATION);
            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        return !empty($fields) ? $fields : $this->grid->getFilter()->getFields();
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    public function getSorts(\ReflectionClass $reflectionClass)
    {
        if ($this->isProduction()) {
            $this->grid->getSort()->getFields();
        }

        /** @var \ReflectionProperty $property */
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $property) {
            $propertyAnnotations = $this->extractorFactory->extract($property, Extractor::PROPERTY_ANNOTATION);
            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Sort) {
                    $fields[] = $property->getName();
                }
            }
        }

        return !empty($fields) ? $fields : $this->grid->getSort()->getFields();
    }

    /**
     * @return bool
     */
    private function isProduction()
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return true;
        }

        return false;
    }
}
