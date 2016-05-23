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
class MenuNotFoundException extends \InvalidArgumentException
{
    public function __construct($menu, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Menu with name "%s" not found.', $menu), $code, $previous);
    }
}
