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

use SymfonyId\AdminBundle\Model\SoftDeleteAwareInterface;
use SymfonyId\AdminBundle\Model\SoftDeleteAwareTrait;
use SymfonyId\AdminBundle\Model\TimestampAwareInterface;
use SymfonyId\AdminBundle\Model\TimestampAwareTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AdvancedUser extends User implements SoftDeleteAwareInterface, TimestampAwareInterface
{
    use SoftDeleteAwareTrait;

    use TimestampAwareTrait;

    public function __construct()
    {
        parent::__construct();
    }
}
