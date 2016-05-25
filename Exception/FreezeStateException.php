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
class FreezeStateException extends \RuntimeException
{
    /**
     * @param string          $object
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($object, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('The object of "%s" has been frozen.', get_class($object)), $code, $previous);
    }
}
