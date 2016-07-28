<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RouteCollectionCompiler
{
    /**
     * @var Extractor
     */
    private $extractorFactory;

    /**
     * @param Extractor $extractorFactory
     */
    public function __construct(Extractor $extractorFactory)
    {
        $this->extractorFactory = $extractorFactory;
    }

    /**
     * @param string            $prefixName
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     * @param Route|null        $route
     *
     * @return RouteCollection
     */
    public function compileRoute($prefixName, \ReflectionClass $class, \ReflectionMethod $method, Route $route = null)
    {
        $collection = new RouteCollection();

        $routeAnnotations = array();
        $methodAnnotaion = null;

        /*
         * Parse method annotation
         */
        $methodAnnotaions = $this->extractorFactory->extract($method, Extractor::METHOD_ANNOTATAION);
        foreach ($methodAnnotaions as $key => $annoation) {
            if ($annoation instanceof Route) {
                $routeAnnotations[] = $annoation;
            }
            if ($annoation instanceof Method) {
                $methodAnnotaion = $annoation;
            }
        }

        $name = $route->getName() ?: strtolower($prefixName.'_'.$method->getName());
        if (empty($routeAnnotations)) {
            $this->addRoute($class, $method, $collection, $name, $route, null, null);
        } else {
            foreach ($routeAnnotations as $routeAnnotation) {
                /* @var Route $routeAnnotation */
                $this->addRoute($class, $method, $collection, $name, $route, $routeAnnotation, $methodAnnotaion);
            }
        }

        return $collection;
    }

    /**
     * @param \ReflectionClass $reflectionController
     *
     * @return Route
     *
     * @throws RuntimeException
     */
    public function extractRouteFromController(\ReflectionClass $reflectionController)
    {
        $classAnnotations = $this->extractorFactory->extract($reflectionController, Extractor::CLASS_ANNOTATION);
        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                return $annotation;
            }
        }

        throw new RuntimeException(sprintf('Class "%s" does not has any Route annotation', $reflectionController->getName()));
    }

    /**
     * @param \ReflectionClass  $reflectionClass
     * @param \ReflectionMethod $reflectionMethod
     * @param RouteCollection   $collection
     * @param string            $name
     * @param Route|null        $controllerRoute
     * @param Route|null        $route
     * @param Method|null       $method
     */
    private function addRoute(\ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod, RouteCollection $collection, $name, Route $controllerRoute = null, Route $route = null, Method $method = null)
    {
        $controller = $reflectionClass->getName().'::'.$reflectionMethod->getName();
        $methodName = str_replace('action', '', strtolower($reflectionMethod->getName()));

        /*
         * Compile route
         */
        $loop = true;
        $index = 0;
        while ($loop) {
            if ('list' === $methodName && 0 === $index) {
                $loop = true;
                ++$index;
            } else {
                $loop = false;
            }

            $routeAction = $route ?: MethodNameToRouteConverter::convert($methodName, $loop);
            $methodAction = $method ?: MethodNameToMethodConverter::convert($methodName);

            $path = '';
            if ($controllerRoute) {
                $path = $controllerRoute->getPath();
            }
            $path = $path.$routeAction->getPath();

            /*
             * Create route
             */
            $symfonyRoute = new SymfonyRoute(
                $path,
                array_merge($routeAction->getDefaults(), array('_controller' => $controller)),
                $routeAction->getRequirements(),
                array_merge($routeAction->getOptions(), array('expose' => true)),
                $routeAction->getHost(),
                $routeAction->getSchemes(),
                $method ? $method->getMethods() : $methodAction->getMethods(),
                $routeAction->getCondition()
            );

            $routeName = $route && $route->getName() ? $route->getName() : substr(str_replace(array('bundle', 'controller', '__'), array('', '', '_'), $name), 0, -6);
            $collection->add(UniqueRouteNameGenerator::generate($collection, $routeName), $symfonyRoute);
        }
    }
}
