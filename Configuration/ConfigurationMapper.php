<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Annotation\Page;
use SymfonyId\AdminBundle\Annotation\Plugin;
use SymfonyId\AdminBundle\Annotation\Template;
use SymfonyId\AdminBundle\Annotation\Util;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationMapper
{
    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var array
     */
    private $fieldsFilter;

    /**
     * @param ExtractorFactory $extractorFactory
     */
    public function __construct(ExtractorFactory $extractorFactory)
    {
        $this->extractorFactory = $extractorFactory;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param array $fieldsFilter
     */
    public function setFieldsFilter(array $fieldsFilter)
    {
        $this->fieldsFilter = $fieldsFilter;
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     * @param \ReflectionClass    $reflectionClass
     *
     * @return ConfiguratorFactory
     */
    public function map(ConfiguratorFactory $configuratorFactory, \ReflectionClass $reflectionClass)
    {
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        /** @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
        /** @var UtilConfigurator $utilConfigurator */
        $utilConfigurator = $configuratorFactory->getConfigurator(UtilConfigurator::class);

        $this->extractorFactory->extract($reflectionClass);
        foreach ($this->extractorFactory->getClassAnnotations() as $annotation) {
            if ($annotation instanceof Crud) {
                $crudConfigurator->setCrud($this->mergeCrudConfiguration($crudConfigurator->getCrud(), $annotation));
                $configuratorFactory->addConfigurator($crudConfigurator);
            }

            if ($annotation instanceof Page) {
                $pageConfigurator->setPage($annotation);
                $configuratorFactory->addConfigurator($pageConfigurator);
            }

            if ($annotation instanceof Grid) {
                $gridConfigurator->setGrid($this->mergeGridConfiguration($gridConfigurator->getGrid(), $annotation));
                $configuratorFactory->addConfigurator($gridConfigurator);
            }

            if ($annotation instanceof Plugin) {
                $pluginConfigurator->setPlugin($annotation);
                $configuratorFactory->addConfigurator($pluginConfigurator);
            }

            if ($annotation instanceof Util) {
                $utilConfigurator->setUtil($annotation);
                $configuratorFactory->addConfigurator($utilConfigurator);
            }
        }

        return $configuratorFactory;
    }

    /**
     * @param Crud $default
     * @param Crud $passed
     *
     * @return Crud
     */
    private function mergeCrudConfiguration(Crud $default, Crud $passed)
    {
        return new Crud(array(
            'modelClass' => $passed->getModelClass() ?: $default->getModelClass(),
            'form' => $passed->getForm() ?: $default->getForm(),
            'menu' => $passed->getMenu() ?: $default->getMenu(),
            'showFields' => $passed->getShowFields() ?: $default->getShowFields(),
            'template' => $passed->getTemplate() ?: $this->template,
            'allowCreate' => $passed->isAllowCreate() ?: $default->isAllowCreate(),
            'allowEdit' => $passed->isAllowEdit() ?: $default->isAllowEdit(),
            'allowShow' => $passed->isAllowShow() ?: $default->isAllowShow(),
            'allowDelete' => $passed->isAllowDelete() ?: $default->isAllowDelete(),
        ));
    }

    /**
     * @param Grid $default
     * @param Grid $passed
     *
     * @return Grid
     */
    private function mergeGridConfiguration(Grid $default, Grid $passed)
    {
        return new Grid(array(
            'column' => $passed->getColumn() ?: $default->getColumn(),
            'filter' => $passed->getFilter() ?: $this->fieldsFilter,
            'sort' => $passed->getSort() ?: $default->getColumn(),
        ));
    }
}
