<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Exception;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ExtractorException extends \InvalidArgumentException
{
    /**
     * ExtractorException constructor.
     *
     * @param string          $expectation
     * @param object          $expected
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($expectation, $expected, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('extract() need %s method as parameter, got %s', $expectation, get_class($expected)), $code, $previous);
    }
}
