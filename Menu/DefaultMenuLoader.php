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

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Menu;
use SymfonyId\AdminBundle\Annotation\Security;
use SymfonyId\AdminBundle\Cache\CacheHandler;
use SymfonyId\AdminBundle\Controller\CrudController;
use SymfonyId\AdminBundle\Controller\UserController;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultMenuLoader extends AbstractMenuLoader implements MenuLoaderInterface
{
    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var CacheHandler
     */
    protected $cacheHandler;

    /**
     * @var Extractor
     */
    protected $extractorFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @param MenuFactory                   $menuFactory
     * @param Router                        $router
     * @param CacheHandler                  $cacheHandler
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Extractor                     $extractorFactory
     * @param TranslatorInterface           $translator
     * @param string                        $translationDomain
     */
    public function __construct(MenuFactory $menuFactory, Router $router, CacheHandler $cacheHandler, AuthorizationCheckerInterface $authorizationChecker, Extractor $extractorFactory, TranslatorInterface $translator, $translationDomain)
    {
        $this->menuFactory = $menuFactory;
        $this->router = $router;
        $this->cacheHandler = $cacheHandler;
        $this->extractorFactory = $extractorFactory;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        parent::__construct($authorizationChecker);
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        $rootMenu = $this->createRootMenu($this->menuFactory);
        $this->addDefaultMenu($rootMenu);
        if ($this->isGranted('ROLE_SUPER_ADMIN') && $this->includeDefaultMenu) {
            $this->addAdminMenu($rootMenu);
        }

        $routeCollection = $this->router->getRouteCollection()->all();
        $matches = array();
        /** @var Route $route */
        foreach ($routeCollection as $name => $route) {
            if (preg_match('/\/list\//', $route->getPath())) {
                //Only /list/ route
                $matches[$name] = $route;
            }
        }

        $reflection = new \ReflectionObject($this);
        $menuItems = array();
        if ($this->cacheHandler->hasCache($reflection)) {
            $menuItems = require $this->cacheHandler->loadCache($reflection);
        } else {
            /** @var Route $route */
            foreach ($matches as $name => $route) {
                if ($temp = $route->getDefault('_controller')) {
                    $controller = explode('::', $temp);

                    $reflectionController = new \ReflectionClass($controller[0]);
                    $classAnnotations = $this->extractorFactory->extract($reflectionController, Extractor::CLASS_ANNOTATION);

                    $menuItems = array_merge($menuItems, $this->getItemFromController(
                        $classAnnotations,
                        $reflectionController,
                        $name
                    ));
                }
            }

            $this->cacheHandler->writeCache($reflection, $menuItems);
        }

        $this->generateMenu($rootMenu, $menuItems);

        return $rootMenu;
    }

    /**
     * @param ItemInterface $parentMenu
     * @param string        $routeName
     * @param string        $menuLabel
     * @param string        $icon
     * @param string        $classCss
     *
     * @return ItemInterface
     */
    protected function addMenu(ItemInterface $parentMenu, $routeName, $menuLabel, $icon = 'fa-bars', $classCss = '')
    {
        $classCss = $classCss.' treeview';

        return $parentMenu->addChild($menuLabel, array(
            'route' => $routeName,
            'label' => sprintf('<i class="fa %s" aria-hidden="true"></i> <span>%s</span>', $icon, $this->translator->trans($menuLabel, array(), $this->translationDomain)),
            'extras' => array(
                'safe_label' => true,
                'translation_domain' => false,
            ),
            'attributes' => array(
                'class' => $classCss,
            ),
        ));
    }

    /**
     * @param ItemInterface $parentMenu
     * @param array         $menuItems
     */
    protected function generateMenu(ItemInterface $parentMenu, array $menuItems)
    {
        foreach ($menuItems as $route => $item) {
            $this->addMenu($parentMenu, $route, $item['name'], $item['icon'], $item['extra']);
        }
    }

    /**
     * @param array $classAnnotations
     * @param \ReflectionClass $reflectionController
     * @param $name
     * @return array
     */
    protected function getItemFromController(array $classAnnotations, \ReflectionClass $reflectionController, $name)
    {
        $menuItems = array();
        /** @var \ReflectionClass $annotation */
        foreach ($classAnnotations as $annotation) {
            if (!($reflectionController->isSubclassOf(CrudController::class) && $reflectionController->getName() !== UserController::class)) {
                continue;
            }

            $menu = new Menu();
            if ($annotation instanceof Crud && $annotation->getMenu()) {
                $menu = $annotation->getMenu();
            }

            $security = new Security();
            if ($annotation instanceof Security) {
                $security = $annotation;
            }

            if ($this->isGranted($security->getRead())) {
                $menuItems[$name] = array(
                    'name' => sprintf(
                        'menu.label.%s',
                        strtolower(str_replace('Controller', '', $reflectionController->getShortName()))
                    ),
                    'icon' => $menu->getIcon(),
                    'extra' => $menu->getExtra(),
                );
            }
        }

        return $menuItems;
    }
}
