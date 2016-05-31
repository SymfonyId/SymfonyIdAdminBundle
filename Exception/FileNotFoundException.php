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
class FileNotFoundException extends \InvalidArgumentException
{
    public function __construct($fileName, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('File "%s" not found', $fileName), $code, $previous);
    }
}
