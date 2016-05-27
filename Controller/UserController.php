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
use SymfonyId\AdminBundle\Annotation as Siab;

/**
 * @Route("/user")
 *
 * @Siab\Page(title="page.user.title", description="page.user.description")
 * @Siab\Plugin(fileChooser=true)
 * @Siab\Util(upload=@Siab\Upload(uploadable="file", targetField="avatar"))
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
        return get_class($this);
    }
}
