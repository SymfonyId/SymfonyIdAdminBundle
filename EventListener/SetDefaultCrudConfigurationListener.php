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
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Template;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SetDefaultCrudConfigurationListener implements CrudControllerListenerAwareInterface, ConfigurationAwareInterface
{
    use CrudControllerListenerAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var Template
     */
    private $template;

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $reflectionController = new \ReflectionObject($this->controller);
        $configuratorFactory = $this->getConfiguratorFactory($reflectionController->getName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $crudConfiguration->getModelClass(),
            'form' => $crudConfiguration->getForm(),
            'menu' => $crudConfiguration->getMenu(),
            'showFields' => $crudConfiguration->getShowFields(),
            'template' => $this->template ?: $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);
    }
}
