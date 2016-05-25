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
     * @param ExtractorFactory $extractorFactory
     */
    public function __construct(ExtractorFactory $extractorFactory)
    {
        $this->extractorFactory = $extractorFactory;
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
                $crudConfigurator->setCrud($annotation);
                $configuratorFactory->addConfigurator($crudConfigurator);
            }

            if ($annotation instanceof Page) {
                $pageConfigurator->setPage($annotation);
                $configuratorFactory->addConfigurator($pageConfigurator);
            }

            if ($annotation instanceof Grid) {
                $gridConfigurator->setGrid($annotation);
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
}
