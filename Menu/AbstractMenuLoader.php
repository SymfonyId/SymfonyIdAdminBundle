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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractMenuLoader
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var bool
     */
    protected $includeDefaultMenu = true;

    /**
     * @param ItemInterface $parentMenu
     * @param string $routeName
     * @param string $menuLabel
     * @param string $icon
     * @param string $classCss
     * @return mixed
     */
    abstract protected function addMenu(ItemInterface $parentMenu, $routeName, $menuLabel, $icon = 'fa-bars', $classCss = '');

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param bool $include
     */
    public function setIncludeDefault($include)
    {
        $this->includeDefaultMenu = $include;
    }

    /**
     * @param FactoryInterface $menuFactory
     *
     * @return ItemInterface
     */
    protected function createRootMenu(FactoryInterface $menuFactory)
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
    protected function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * @param ItemInterface $parentMenu
     */
    protected function addDefaultMenu(ItemInterface $parentMenu)
    {
        $this->addMenu($parentMenu, 'home', 'menu.dashboard');
        $this->addMenu($parentMenu, 'symfonyid_admin_profile_profile', 'menu.profile');
        $this->addMenu($parentMenu, 'symfonyid_admin_profile_changepassword', 'menu.user.change_password');
    }

    /**
     * @param ItemInterface $parentMenu
     */
    protected function addAdminMenu(ItemInterface $parentMenu)
    {
        $this->addMenu($parentMenu, 'symfonyid_admin_user_list', 'menu.user.title');
    }
}
