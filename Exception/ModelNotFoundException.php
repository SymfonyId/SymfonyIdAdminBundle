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
class ModelNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string          $setter
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($setter, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Entity class can\'t empty. Please call "%s" to set entity class', $setter), $code, $previous);
    }
}
