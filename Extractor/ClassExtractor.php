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

use Doctrine\Common\Annotations\Reader;
use SymfonyId\AdminBundle\Exception\ExtractorException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ClassExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \Reflector $reflectionClass
     *
     * @throws
     *
     * @return array
     */
    public function extract(\Reflector $reflectionClass)
    {
        if (!$reflectionClass instanceof \ReflectionClass) {
            throw new ExtractorException(\ReflectionClass::class, $reflectionClass);
        }

        return $this->reader->getClassAnnotations($reflectionClass);
    }
}
