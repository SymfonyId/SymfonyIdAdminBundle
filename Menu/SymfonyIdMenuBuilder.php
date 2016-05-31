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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Cache\CacheHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdMenuBuilder
{
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var MenuLoaderFactory
     */
    private $menuLoaderFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var CacheHandler
     */
    private $cacheHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @var string
     */
    private $menuLoader;

    /**
     * Used by Yaml menu loader
     *
     * @var string
     */
    private $ymlPath;

    /**
     * @param MenuFactory                   $menuFactory
     * @param MenuLoaderFactory             $menuLoaderFactory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CacheHandler                  $cacheHandler
     * @param TranslatorInterface           $translator
     * @param string                        $translationDomain
     */
    public function __construct(MenuFactory $menuFactory, MenuLoaderFactory $menuLoaderFactory, AuthorizationCheckerInterface $authorizationChecker, CacheHandler $cacheHandler, TranslatorInterface $translator, $translationDomain)
    {
        $this->menuFactory = $menuFactory;
        $this->menuLoaderFactory = $menuLoaderFactory;
        $this->authorizationChecker = $authorizationChecker;
        $this->cacheHandler = $cacheHandler;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param string $menuLoader
     */
    public function setMenuLoader($menuLoader)
    {
        $this->menuLoader = $menuLoader;
    }

    /**
     * @param string $ymlPath
     */
    public function setYmlPath($ymlPath)
    {
        $this->ymlPath = $ymlPath;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options)
    {
        $rootMenu = $this->createRootMenu($this->menuFactory);
        $this->addDefaultMenu($rootMenu);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addAdminMenu($rootMenu);
        }

        $reflectionClass = new \ReflectionClass(self::class);
        if ($this->cacheHandler->hasCache($reflectionClass)) {
            $menuItems = require $this->cacheHandler->loadCache($reflectionClass);
        } else {
            $menuItems = $this->menuLoaderFactory->getMenuItems($this->menuLoader, $this->ymlPath);
            $this->cacheHandler->writeCache($reflectionClass, $menuItems);
        }

        $this->generateMenu($rootMenu, $menuItems);

        return $rootMenu;
    }

    /**
     * @param ItemInterface $parentMenu
     * @param string        $routeName
     * @param string        $menuLabel
     * @param string        $icon
     * @param array         $options
     *
     * @return ItemInterface
     */
    private function addChildMenu(ItemInterface $parentMenu, $routeName, $menuLabel, $icon = 'fa-bars', array $options = array())
    {
        $options = $options.' treeview';

        return $parentMenu->addChild($menuLabel, array(
            'route' => $routeName,
            'label' => sprintf('<i class="fa %s" aria-hidden="true"></i> <span>%s</span>', $icon, $this->translator->trans($menuLabel, array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => $options,
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
                $menu = $this->addChildMenu($parentMenu, $route, $item['name'], $item['icon'], $item['extra_css']);
                $this->generateMenu($menu, $item['child']);
            } else {
                $this->addChildMenu($parentMenu, $route, $item['name'], $item['icon'], $item['extra_css']);
            }
        }
    }
}
