<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Exception\DriverNotFoundException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ParameterBuilder
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param string $alias
     * @param array  $config
     */
    public function build($alias, array $config)
    {
        $this->buildGlobalParameter($alias, $config);
        $this->buildThemeParameter($alias, $config);
        $this->buildUserParameter($alias, $config);
    }

    /**
     * @param string $alias
     * @param array  $config
     */
    private function buildGlobalParameter($alias, array $config)
    {
        $this->containerBuilder->setParameter($alias.'.admin.app_title', $config['app_title']);
        $this->containerBuilder->setParameter($alias.'.admin.app_short_title', $config['app_short_title']);
        $this->containerBuilder->setParameter($alias.'.admin.per_page', $config['per_page']);
        $this->containerBuilder->setParameter($alias.'.admin.menu', $config['menu']);
        $this->containerBuilder->setParameter($alias.'.admin.profile_fields', $config['profile_fields']);
        $this->containerBuilder->setParameter($alias.'.admin.identifier', $config['identifier']);
        $this->containerBuilder->setParameter($alias.'.admin.max_records', $config['max_records']);
        $this->containerBuilder->setParameter($alias.'.admin.filter', $config['filter']);
        $this->containerBuilder->setParameter($alias.'.admin.date_time_format', $config['date_time_format']);
        $this->containerBuilder->setParameter($alias.'.admin.translation_domain', $config['translation_domain']);

        if (!in_array($config['driver'], array(Driver::ORM, Driver::ODM))) {
            throw new DriverNotFoundException($config['value']);
        }

        $this->containerBuilder->setParameter($alias.'.admin.driver', $config['driver']);

        $number = array(
            'decimal_precision' => $config['number_format']['decimal_precision'],
            'decimal_separator' => $config['number_format']['decimal_separator'],
            'thousand_separator' => $config['number_format']['thousand_separator'],
        );
        $this->containerBuilder->setParameter($alias.'.admin.number', $number);

        $this->containerBuilder->setParameter($alias.'.admin.upload_directory', array(
            'server_path' => $this->containerBuilder->getParameter('kernel.root_dir').'/../web/'.$config['upload_dir'],
            'web_path' => '/'.$config['upload_dir'].'/',
        ));
    }

    /**
     * @param string $alias
     * @param array  $config
     */
    private function buildThemeParameter($alias, array $config)
    {
        $this->containerBuilder->setParameter($alias.'.admin.themes.dashboard', $config['themes']['dashboard']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.profile', $config['themes']['profile']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.change_password', $config['themes']['change_password']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.form_theme', $config['themes']['form_theme']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.new_view', $config['themes']['new_view']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.bulk_new', $config['themes']['bulk_new_view']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.edit_view', $config['themes']['edit_view']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.show_view', $config['themes']['show_view']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.list_view', $config['themes']['list_view']);
        $this->containerBuilder->setParameter($alias.'.admin.themes.pagination', $config['themes']['pagination']);
    }

    /**
     * @param string $alias
     * @param array  $config
     */
    private function buildUserParameter($alias, array $config)
    {
        $this->containerBuilder->setParameter($alias.'.admin.user.user_form', $config['user']['form_class']);
        $this->containerBuilder->setParameter($alias.'.admin.user.auto_enable', $config['user']['auto_enable']);
        $this->containerBuilder->setParameter($alias.'.admin.user.model_class', $config['user']['model_class']);
        $this->containerBuilder->setParameter($alias.'.admin.user.show_fields', $config['user']['show_fields']);
        $this->containerBuilder->setParameter($alias.'.admin.user.grid_columns', $config['user']['grid_columns']);
        $this->containerBuilder->setParameter($alias.'.admin.user.grid_filters', $config['user']['filters']);
        $this->containerBuilder->setParameter($alias.'.admin.user.password_form', $config['user']['password_form']);
        $this->containerBuilder->setParameter($alias.'.admin.user.default_avatar', 'bundles/symfonianindonesiaadmin/img/apple-icon-114x114.png');
    }
}
