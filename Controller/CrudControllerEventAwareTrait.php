<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait CrudControllerEventAwareTrait
{
    use ContainerAwareTrait;

    /**
     * @var CrudController
     */
    protected $controller;

    /**
     * @param FilterControllerEvent $event
     *
     * @return bool
     */
    public function isValidCrudListener(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return false;
        }

        $controller->setCacheHandler($this->container->get('symfonyid.admin.cache.cache_handler'));
        $controller->setConfiguratorFactory($this->container->get('symfonyid.admin.configuration.configurator_factory'));
        $controller->setKernel($this->container->get('kernel'));

        $this->controller = $controller;

        return true;
    }
}
