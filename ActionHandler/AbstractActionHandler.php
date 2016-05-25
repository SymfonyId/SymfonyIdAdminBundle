<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\ActionHandler;

use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractActionHandler implements ActionHandlerInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var View
     */
    protected $view;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }
}
