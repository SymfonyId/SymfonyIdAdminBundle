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
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationMapper;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;

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
     * @var ConfigurationMapper
     */
    private $configurationMapper;

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
     * @param ConfigurationMapper         $configurationMapper
     * @param ConfiguratorFactory         $configuratorFactory
     * @param ConfigurationCacheWriter    $configurationCacheWriter
     * @param DefaultConfigurationFactory $defaultConfigurationFactory
     */
    public function __construct(Router $router, ConfigurationMapper $configurationMapper, ConfiguratorFactory $configuratorFactory, ConfigurationCacheWriter $configurationCacheWriter, DefaultConfigurationFactory $defaultConfigurationFactory)
    {
        $this->router = $router;
        $this->configurationMapper = $configurationMapper;
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
            $this->cacheWriter->writeCache($controller, $this->configurationMapper->map($configuratorFactory, $controller));
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

                if ($reflectionController->implementsInterface(ConfigurationAwareInterface::class)) {
                    $controllers[] = $reflectionController;
                }
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
}
