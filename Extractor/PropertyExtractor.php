<?php

/*
 * This file is part of the AdminBundle package.
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
class PropertyExtractor implements ExtractorInterface
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
     * @param \Reflector $reflectionProperty
     *
     * @throws
     *
     * @return array
     */
    public function extract(\Reflector $reflectionProperty)
    {
        if (!$reflectionProperty instanceof \ReflectionProperty) {
            throw new ExtractorException(\ReflectionProperty::class, $reflectionProperty);
        }

        return $this->reader->getPropertyAnnotations($reflectionProperty);
    }
}
