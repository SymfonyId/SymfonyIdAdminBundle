<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\ActionHandler;

use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface ActionHandlerInterface
{
    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @param View $view
     */
    public function setView(View $view);

    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver);
}
