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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Grid;
use SymfonyId\AdminBundle\Annotation\Page;
use SymfonyId\AdminBundle\Annotation\Plugin;
use SymfonyId\AdminBundle\Annotation\Util;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Configuration\PageConfigurator;
use SymfonyId\AdminBundle\Configuration\PluginConfigurator;
use SymfonyId\AdminBundle\Configuration\UtilConfigurator;
use SymfonyId\AdminBundle\Controller\CrudController;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudControllerCacheWarmer implements CacheWarmerInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var ConfiguratorFactory
     */
    private $configuratorFactory;

    /**
     * @var ConfigurationCacheWriter
     */
    private $cacheWriter;

    /**
     * @param Router                      $router
     * @param ExtractorFactory            $extractorFactory
     * @param ConfiguratorFactory         $configuratorFactory
     * @param ConfigurationCacheWriter    $configurationCacheWriter
     * @param DefaultConfigurationFactory $defaultConfigurationFactory
     */
    public function __construct(Router $router, ExtractorFactory $extractorFactory, ConfiguratorFactory $configuratorFactory, ConfigurationCacheWriter $configurationCacheWriter, DefaultConfigurationFactory $defaultConfigurationFactory)
    {
        $this->router = $router;
        $this->extractorFactory = $extractorFactory;
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
     * @param string $cacheDirectory
     */
    public function warmUp($cacheDirectory)
    {
        $configuratorFactory = clone $this->configuratorFactory;
        $controllers = $this->getAllControllers();
        foreach ($controllers as $controller) {
            $this->cacheWriter->writeCache($controller, $this->compileControllerConfiguration($configuratorFactory, $controller));
        }
    }

    /**
     * @return \ReflectionClass[]
     */
    private function getAllControllers()
    {
        $controllers = array();
        $routers = $this->router->getRouteCollection()->all();
        /** @var Route $router */
        foreach ($routers as $router) {
            $attribute = $router->getDefaults();
            if (array_key_exists('_controller', $attribute)) {
                $reflectionController = new \ReflectionClass($this->guessControllerClass($attribute['_controller']));

                if ($reflectionController->isSubclassOf(CrudController::class)) {
                    $controllers[] = $reflectionController;
                }

                $controllers[] = $this->guessControllerClass($attribute['_controller']);
            }
        }

        return $controllers;
    }

    /**
     * @param string $routeController
     *
     * @return string
     */
    private function guessControllerClass($routeController)
    {
        $temp = explode(':', $routeController);
        if (3 === count($temp)) {
            $controllerClass = $temp[0];
        } else {
            $controllerClass = get_class($this->container->get($temp[0]));
        }

        return $controllerClass;
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     * @param \ReflectionClass $reflectionController
     *
     * @return ConfiguratorFactory
     */
    private function compileControllerConfiguration(ConfiguratorFactory $configuratorFactory, \ReflectionClass $reflectionController)
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

        $this->extractorFactory->extract($reflectionController);
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
