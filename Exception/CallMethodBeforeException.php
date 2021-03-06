<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
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
class CallMethodBeforeException extends RuntimeException
{
    public function __construct($method, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Please call "%s" before use this method.', $method), $code, $previous);
    }
}
