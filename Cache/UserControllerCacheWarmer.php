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
use SymfonyId\AdminBundle\Annotation\Column;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Annotation\Sort;
use SymfonyId\AdminBundle\Annotation\Template;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Controller\UserController;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserControllerCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ConfiguratorFactory
     */
    private $configuratorFactory;

    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var ConfigurationCacheWriter
     */
    private $cacheWriter;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var array
     */
    private $showFields = array();

    private $gridColumns = array();

    private $gridFilters = array();

    private $gridSorters = array();

    /**
     * @param ConfiguratorFactory         $configuratorFactory
     * @param ExtractorFactory            $extractorFactory
     * @param ConfigurationCacheWriter    $configurationCacheWriter
     * @param DefaultConfigurationFactory $defaultConfigurationFactory
     */
    public function __construct(ConfiguratorFactory $configuratorFactory, ExtractorFactory $extractorFactory, ConfigurationCacheWriter $configurationCacheWriter, DefaultConfigurationFactory $defaultConfigurationFactory)
    {
        $this->configuratorFactory = $configuratorFactory;
        $this->extractorFactory = $extractorFactory;
        $this->cacheWriter = $configurationCacheWriter;
        $defaultConfigurationFactory->build($configuratorFactory);
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param array $showFields
     */
    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @param array $gridColumns
     */
    public function setGridColumns(array $gridColumns)
    {
        $this->gridColumns = $gridColumns;
    }

    /**
     * @param array $gridFilters
     */
    public function setGridFilters(array $gridFilters)
    {
        $this->gridFilters = $gridFilters;
    }

    /**
     * @param array $gridSorters
     */
    public function setGridSorters(array $gridSorters)
    {
        $this->gridSorters = $gridSorters;
    }

    /**
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * @param string $cacheDirectory
     */
    public function warmUp($cacheDirectory)
    {
        $configuratorFactory = clone $this->configuratorFactory;

        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);

        $this->overrideCrudConfiguration($configuratorFactory, $crudConfigurator);
        $this->overrideGridConfiguration($configuratorFactory, $gridConfigurator);

        $this->cacheWriter->writeCache(new \ReflectionClass(UserController::class), $configuratorFactory);
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     * @param CrudConfigurator    $crudConfigurator
     */
    private function overrideCrudConfiguration(ConfiguratorFactory $configuratorFactory, CrudConfigurator $crudConfigurator)
    {
        $crudConfiguration = $crudConfigurator->getCrud();
        $crud = new Crud(array(
            'modelClass' => $this->modelClass ?: $crudConfiguration->getModelClass(),
            'form' => $this->form ?: $crudConfiguration->getForm(),
            'menu' => $crudConfiguration->getMenu(),
            'showFields' => empty($this->showFields) ? $crudConfiguration->getShowFields() : $this->showFields,
            'template' => $this->template,
            'allowCreate' => $crudConfiguration->isAllowCreate(),
            'allowEdit' => $crudConfiguration->isAllowEdit(),
            'allowShow' => $crudConfiguration->isAllowShow(),
            'allowDelete' => $crudConfiguration->isAllowDelete(),
        ));
        $crudConfigurator->setCrud($crud);
        $configuratorFactory->addConfigurator($crudConfigurator);
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     * @param GridConfigurator    $gridConfigurator
     */
    private function overrideGridConfiguration(ConfiguratorFactory $configuratorFactory, GridConfigurator $gridConfigurator)
    {
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $gridConfiguration = $gridConfigurator->getGrid();
        $grid = new Grid(array(
            'column' => empty($this->gridColumns) ? $gridConfiguration->getColumn() : new Column(array('value' => $this->gridColumns)),
            'filter' => empty($this->gridFilters) ? $gridConfiguration->getFilter() : new Filter(array('value' => $this->gridFilters)),
            'sort' => empty($this->gridSorters) ? $gridConfiguration->getSort() : new Sort(array('value' => $this->gridSorters)),
        ));
        $gridConfigurator->setGrid($grid);
        $configuratorFactory->addConfigurator($gridConfigurator);
    }
}
