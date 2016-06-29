<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerInjectionListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param FilterControllerEvent $event
     * @return bool
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];

        if ($controller instanceof ConfigurationAwareInterface) {
            $controller->setKernel($this->container->get('kernel'));
            $controller->setCacheHandler($this->container->get('symfonyid.admin.cache.cache_handler'));
            $controller->setConfiguratorFactory($this->container->get('symfonyid.admin.configuration.configurator_factory'));
        }
    }

}