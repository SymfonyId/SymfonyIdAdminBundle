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
class MethodExtractor implements ExtractorInterface
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
     * @param \Reflector $reflectionMethod
     *
     * @throws
     *
     * @return array
     */
    public function extract(\Reflector $reflectionMethod)
    {
        if (!$reflectionMethod instanceof \ReflectionMethod) {
            throw new ExtractorException(\ReflectionMethod::class, $reflectionMethod);
        }

        return $this->reader->getMethodAnnotations($reflectionMethod);
    }
}
