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
use SymfonyId\AdminBundle\Cache\CacheHandler;
use SymfonyId\AdminBundle\Controller\CrudController;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

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
     * @var ExtractorFactory
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
     * @param ExtractorFactory              $extractorFactory
     * @param TranslatorInterface           $translator
     * @param string                        $translationDomain
     */
    public function __construct(MenuFactory $menuFactory, Router $router, CacheHandler $cacheHandler, AuthorizationCheckerInterface $authorizationChecker, ExtractorFactory $extractorFactory, TranslatorInterface $translator, $translationDomain)
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
            $menuItems = $this->cacheHandler->loadCache($reflection);
        } else {
            /** @var Route $route */
            foreach ($matches as $name => $route) {
                if ($temp = $route->getDefault('_controller')) {
                    $controller = explode('::', $temp);

                    $reflectionController = new \ReflectionClass($controller[0]);
                    $this->extractorFactory->extract($reflectionController);
                    foreach ($this->extractorFactory->getClassAnnotations() as $annotation) {
                        if ($annotation instanceof Crud && $reflectionController->isSubclassOf(CrudController::class)) {
                            $menu = $annotation->getMenu() ?: new Menu();

                            $menuItems[$name] = array(
                                'name' => $this->translator->trans(sprintf('menu.label.%s', strtolower(str_replace('Controller', '', $reflectionController->getShortName()))), array(), $this->translationDomain),
                                'icon' => $menu->getIcon(),
                                'extra' => $menu->getExtra(),
                            );
                        }
                    }
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
            'extras' => array('safe_label' => true),
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
}
