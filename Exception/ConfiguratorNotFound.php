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
class ConfiguratorNotFound extends \InvalidArgumentException
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Configurator with name "%s" not found.', $message), $code, $previous);
    }
}
