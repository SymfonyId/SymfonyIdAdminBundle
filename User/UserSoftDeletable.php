<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\User;

use SymfonyId\AdminBundle\Model\SoftDeletableInterface;
use SymfonyId\AdminBundle\Model\SoftDeletableTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class UserSoftDeletable extends User implements SoftDeletableInterface
{
    use SoftDeletableTrait;

    public function __construct()
    {
        parent::__construct();
    }
}