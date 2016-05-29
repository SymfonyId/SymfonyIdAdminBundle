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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use SymfonyId\AdminBundle\Controller\ControllerFinder;
use SymfonyId\AdminBundle\Controller\CrudController;
use SymfonyId\AdminBundle\Controller\HomeController;
use SymfonyId\AdminBundle\Controller\ProfileController;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdRouteLoader extends DelegatingLoader
{
    const METHOD_NEW = 'new';
    const METHOD_DOWNLOAD = 'download';
    const METHOD_LIST = 'list';
    const METHOD_EDIT = 'edit';
    const METHOD_SHOW = 'show';
    const METHOD_DELETE = 'delete';
    const METHOD_BULK_DELETE = 'bulkdelete';
    const METHOD_BULK_NEW = 'bulknew';

    /**
     * @var array
     */
    public static $VALID_CRUD_METHODS = array(
        self::METHOD_BULK_DELETE,
        self::METHOD_BULK_NEW,
        self::METHOD_DELETE,
        self::METHOD_DOWNLOAD,
        self::METHOD_EDIT,
        self::METHOD_LIST,
        self::METHOD_NEW,
        self::METHOD_SHOW,
    );

    /**
     * @var ControllerFinder
     */
    private $controllerFinder;

    /**
     * @var RouteCollectionCompiler
     */
    private $routeCollectionCompiler;

    /**
     * @param ControllerNameParser    $parser
     * @param LoaderResolverInterface $resolver
     * @param ControllerFinder        $controllerFinder
     * @param RouteCollectionCompiler $routeCollectionCompiler
     */
    public function __construct(ControllerNameParser $parser, LoaderResolverInterface $resolver, ControllerFinder $controllerFinder, RouteCollectionCompiler $routeCollectionCompiler)
    {
        parent::__construct($parser, $resolver);
        $this->controllerFinder = $controllerFinder;
        $this->routeCollectionCompiler = $routeCollectionCompiler;
    }

    /**
     * @param string $resource
     * @param null   $type
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();
        $controllers = $this->controllerFinder->getAllControllerFromResource($resource);
        /** @var \ReflectionClass $controller */
        foreach ($controllers as $controller) {
            if (!$controller) {
                //bugfix for windows OS
                continue;
            }

            if ($controller->isSubclassOf(CrudController::class) || $controller->getName() === HomeController::class || $controller->getName() === ProfileController::class) {
                $this->registerRoute($collection, $controller);
            } else {
                $collection->addCollection(parent::load($resource, 'annotation'));
            }
        }

        return $collection;
    }

    /**
     * @param string $resource
     * @param null   $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'symfonyid' === $type;
    }

    /**
     * @param RouteCollection  $collection
     * @param \ReflectionClass $controller
     */
    private function registerRoute(RouteCollection $collection, \ReflectionClass $controller)
    {
        $route = $this->routeCollectionCompiler->extractRouteFromController($controller) ?: new Route(array('path' => ''));

        foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos(strtolower($method), 'action')) {
                $prefixName = str_replace('\\', '_', $controller->getName());
                $collection->addCollection($this->routeCollectionCompiler->compileRoute($prefixName, $controller, $method, $route));
            }
        }
    }
}
