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

use SymfonyId\AdminBundle\Model\TimestampableInterface;
use SymfonyId\AdminBundle\Model\TimestampableTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class UserTimestampable extends User implements TimestampableInterface
{
    use TimestampableTrait;

    public function __construct()
    {
        parent::__construct();
    }
}
