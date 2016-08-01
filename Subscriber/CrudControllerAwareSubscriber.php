<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfigurationMapper;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareInterface;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareTrait;
use SymfonyId\AdminBundle\Controller\UserController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudControllerAwareSubscriber implements CrudControllerEventAwareInterface, EventSubscriberInterface
{
    use CrudControllerEventAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var ConfigurationMapper
     */
    private $configurationMapper;

    /**
     * @param KernelInterface     $kernel
     * @param ConfigurationMapper $configurationMapper
     */
    public function __construct(KernelInterface $kernel, ConfigurationMapper $configurationMapper)
    {
        $this->kernel = $kernel;
        $this->configurationMapper = $configurationMapper;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Exception
     */
    public function extractAnnotation(FilterControllerEvent $event)
    {
        if ($this->isProduction()) {
            return;
        }

        if (!$this->isValidCrudListener($event)) {
            return;
        }

        if ($this->controller instanceof UserController) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        $this->configurationMapper->map($configuratorFactory, $reflectionController);
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @return bool
     */
    public function setControllerDependencies(FilterControllerEvent $event)
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

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('extractAnnotation', 127),
                array('setControllerDependencies', 0),
            ),
        );
    }
}
