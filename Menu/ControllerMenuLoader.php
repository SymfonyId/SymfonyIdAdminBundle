<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Menu;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Controller\UserController;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerMenuLoader implements MenuLoaderInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @param Router              $router
     * @param ExtractorFactory    $extractorFactory
     * @param TranslatorInterface $translator
     * @param string              $translationDomain
     */
    public function __construct(Router $router, ExtractorFactory $extractorFactory, TranslatorInterface $translator, $translationDomain)
    {
        $this->router = $router;
        $this->extractorFactory = $extractorFactory;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @return array
     */
    public function getMenuItems()
    {
        $routeCollection = $this->router->getRouteCollection()->all();
        $matches = array();
        /** @var Route $route */
        foreach ($routeCollection as $name => $route) {
            if (preg_match('/\/list\//', $route->getPath())) {
                //Only /list/ route
                $matches[$name] = $route;
            }
        }

        $menuItems = array();
        /** @var Route $route */
        foreach ($matches as $name => $route) {
            if ($temp = $route->getDefault('_controller')) {
                $controller = explode('::', $temp);

                $reflectionController = new \ReflectionClass($controller[0]);
                $this->extractorFactory->extract($reflectionController);
                foreach ($this->extractorFactory->getClassAnnotations() as $annotation) {
                    if ($annotation instanceof Crud && !$annotation instanceof UserController) {
                        $menuItems[$name] = array(
                            'name' => $this->translator->trans(sprintf('menu.label.%s', strtolower(str_replace('Controller', '', $reflectionController->getShortName()))), array(), $this->translationDomain),
                            'icon' => $annotation->getMenuIcon(),
                        );
                    }
                }
            }
        }

        return $menuItems;
    }
}
