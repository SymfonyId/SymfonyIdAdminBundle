<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Manager;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DriverFinder
{
    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var string
     */
    private $defaultDriver;

    /**
     * @param ExtractorFactory $extractorFactory
     * @param string           $defaultDriver
     */
    public function __construct(ExtractorFactory $extractorFactory, $defaultDriver)
    {
        $this->extractorFactory = $extractorFactory;
        $this->defaultDriver = $defaultDriver;
    }

    /**
     * @param string $class
     *
     * @return Driver
     */
    public function findDriverForClass($class)
    {
        $this->extractorFactory->extract(new \ReflectionClass($class));
        foreach ($this->extractorFactory->getClassAnnotations() as $annotation) {
            if ($annotation instanceof Driver) {
                return $annotation;
            }
        }

        return new Driver(array('value' => $this->defaultDriver));
    }
}
