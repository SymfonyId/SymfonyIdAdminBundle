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
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfigurationMapper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerAnnotationExtractorListener implements CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
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
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->isProduction()) {
            return;
        }

        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        $this->configurationMapper->map($configuratorFactory, $reflectionController);
    }
}
