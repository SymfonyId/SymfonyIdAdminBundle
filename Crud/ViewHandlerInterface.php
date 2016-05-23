<?php

namespace SymfonyId\AdminBundle\Crud;

use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\View\View;

interface ViewHandlerInterface
{
    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver);
}
