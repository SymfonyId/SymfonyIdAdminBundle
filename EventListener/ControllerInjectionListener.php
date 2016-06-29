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

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerInjectionListener implements CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        if ($this->controller instanceof ConfigurationAwareInterface) {
            $this->controller->setKernel($this->container->get('kernel'));
            $this->controller->setCacheHandler($this->container->get('symfonyid.admin.cache.cache_handler'));
            $this->controller->setConfiguratorFactory($this->container->get('symfonyid.admin.configuration.configurator_factory'));
        }
    }

}