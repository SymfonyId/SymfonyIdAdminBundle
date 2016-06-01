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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Annotation\Menu;
use SymfonyId\AdminBundle\Controller\CrudController;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultMenuLoader implements MenuLoaderInterface
{
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

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
     * @param MenuFactory                   $menuFactory
     * @param Router                        $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ExtractorFactory              $extractorFactory
     * @param TranslatorInterface           $translator
     * @param string                        $translationDomain
     */
    public function __construct(MenuFactory $menuFactory, Router $router, AuthorizationCheckerInterface $authorizationChecker, ExtractorFactory $extractorFactory, TranslatorInterface $translator, $translationDomain)
    {
        $this->menuFactory = $menuFactory;
        $this->router = $router;
        $this->extractorFactory = $extractorFactory;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        $rootMenu = $this->createRootMenu($this->menuFactory);
        $this->addDefaultMenu($rootMenu);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
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

        $menuItems = array();
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
    private function addChildMenu(ItemInterface $parentMenu, $routeName, $menuLabel, $icon = 'fa-bars', $classCss = '')
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
     * @param FactoryInterface $menuFactory
     *
     * @return ItemInterface
     */
    private static function createRootMenu(FactoryInterface $menuFactory)
    {
        $menu = $menuFactory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu',
            ),
        ));

        return $menu;
    }

    /**
     * @param $role
     *
     * @return bool
     */
    private function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * @param ItemInterface $parentMenu
     */
    private function addDefaultMenu(ItemInterface $parentMenu)
    {
        $this->addChildMenu($parentMenu, 'home', 'menu.dashboard');
        $this->addChildMenu($parentMenu, 'symfonyid_admin_profile_profile', 'menu.profile');
        $this->addChildMenu($parentMenu, 'symfonyid_admin_profile_changepassword', 'menu.user.change_password');
    }

    /**
     * @param ItemInterface $parentMenu
     */
    private function addAdminMenu(ItemInterface $parentMenu)
    {
        $this->addChildMenu($parentMenu, 'symfonyid_admin_user_list', 'menu.user.title');
    }

    /**
     * @param ItemInterface $parentMenu
     * @param array         $menuItems
     */
    private function generateMenu(ItemInterface $parentMenu, array $menuItems)
    {
        foreach ($menuItems as $route => $item) {
            if (array_key_exists('child', $item)) {
                $menu = $this->addChildMenu($parentMenu, $route, $item['name'], $item['icon'], $item['extra']);
                $this->generateMenu($menu, $item['child']);
            } else {
                $this->addChildMenu($parentMenu, $route, $item['name'], $item['icon'], $item['extra']);
            }
        }
    }
}
