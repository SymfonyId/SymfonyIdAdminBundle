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
class MethodNotValidException extends \InvalidArgumentException
{
    /**
     * @param string          $methodName
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($methodName, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Method "%s" is not valid.', $methodName), $code, $previous);
    }
}
