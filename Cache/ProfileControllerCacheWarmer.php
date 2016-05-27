<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Controller\ProfileController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ProfileControllerCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ConfiguratorFactory
     */
    private $configuratorFactory;

    /**
     * @var ConfigurationCacheWriter
     */
    private $cacheWriter;

    /**
     * @var string
     */
    private $form;

    /**
     * @var array
     */
    private $showFields = array();

    /**
     * @param ConfiguratorFactory         $configuratorFactory
     * @param ConfigurationCacheWriter    $configurationCacheWriter
     * @param DefaultConfigurationFactory $defaultConfigurationFactory
     */
    public function __construct(ConfiguratorFactory $configuratorFactory, ConfigurationCacheWriter $configurationCacheWriter, DefaultConfigurationFactory $defaultConfigurationFactory)
    {
        $this->configuratorFactory = $configuratorFactory;
        $this->cacheWriter = $configurationCacheWriter;
        $defaultConfigurationFactory->build($configuratorFactory);
    }

    /**
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @param array $showFields
     */
    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @param string $cacheDirectory
     */
    public function warmUp($cacheDirectory)
    {
        $configuratorFactory = clone $this->configuratorFactory;

        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $crudConfiguration->getModelClass(),
            'form' => $this->form ?: $crudConfiguration->getForm(),
            'menu' => $crudConfiguration->getMenu(),
            'showFields' => empty($this->showFields) ? $crudConfiguration->getShowFields() : $this->showFields,
            'template' => $crudConfiguration->getTemplate(),
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);
        $configuratorFactory->addConfigurator($crudConfigurator);

        $this->cacheWriter->writeCache(new \ReflectionClass(ProfileController::class), $configuratorFactory);
    }
}
