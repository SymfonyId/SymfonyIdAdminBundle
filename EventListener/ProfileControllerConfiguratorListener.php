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
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Controller\ProfileController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ProfileControllerConfiguratorListener
{
    /**
     * @var ConfiguratorFactory
     */
    private $configuratorFactory;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $profileFields;

    /**
     * @var string
     */
    private $formClass;

    /**
     * @param ConfiguratorFactory $configuratorFactory
     * @param KernelInterface     $kernel
     */
    public function __construct(ConfiguratorFactory $configuratorFactory, KernelInterface $kernel)
    {
        $this->configuratorFactory = $configuratorFactory;
        $this->kernel;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * @param array $profileFields
     */
    public function setProfileFields(array $profileFields)
    {
        $this->profileFields = $profileFields;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];
        if (!$controller instanceof ProfileController) {
            return;
        }

        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $crudConfiguration->getModelClass(),
            'form' => $this->formClass,
            'menuIcon' => $crudConfiguration->getMenuIcon(),
            'showFields' => $this->profileFields,
            'template' => $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));

        $crudConfigurator->setCrud($crud);
    }
}
