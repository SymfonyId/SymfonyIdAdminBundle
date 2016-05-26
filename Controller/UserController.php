<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SymfonyId\AdminBundle\Annotation\Page;
use SymfonyId\AdminBundle\Annotation\Plugin;
use SymfonyId\AdminBundle\Annotation\Upload;
use SymfonyId\AdminBundle\Annotation\Util;

/**
 * @Route("/user")
 *
 * @Page(title="page.user.title", description="page.user.description")
 * @Plugin(fileChooser=true)
 * @Util(upload=@Upload(uploadable="file", targetField="avatar"))
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserController extends CrudController
{
    /**
     * @return string
     */
    protected function getClassName()
    {
        return __CLASS__;
    }
}
