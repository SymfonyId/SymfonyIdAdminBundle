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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationTreeBuilder
{
    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function build(ArrayNodeDefinition $rootNode)
    {
        $this->buildRootConfiguration($rootNode);
        $this->buildMenuConfiguration($rootNode);
        $this->buildNumberFormatConfiguration($rootNode);
        $this->buildUserConfiguration($rootNode);
        $this->buildThemeConfiguration($rootNode);
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildRootConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('app_title')->defaultValue(Constants::APP_DESCRIPTION)->end()
                ->scalarNode('app_short_title')->defaultValue(Constants::APP_TITLE)->end()
                ->integerNode('per_page')->defaultValue(10)->end()
                ->scalarNode('identifier')->defaultValue('id')->end()
                ->integerNode('max_records')->defaultValue(1000)->end()
                ->scalarNode('date_time_format')->defaultValue('d-m-Y')->end()
                ->scalarNode('upload_dir')->defaultValue('uploads')->end()
                ->enumNode('driver')
                    ->values(array(Driver::ORM, Driver::ODM, Driver::BOTH))
                    ->isRequired()
                ->end()
                ->scalarNode('translation_domain')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('filters')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    private function buildMenuConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('symfonyid_menu')->end()
                        ->scalarNode('loader')->defaultValue('symfonyid.admin.menu.menu_loader')->end()
                        ->scalarNode('path')->defaultValue(null)->end()
                        ->booleanNode('include_default')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildNumberFormatConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('number_format')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('decimal_precision')->defaultValue(0)->end()
                        ->scalarNode('decimal_separator')->defaultValue(',')->end()
                        ->scalarNode('thousand_separator')->defaultValue('.')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildUserConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('user')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('profile_fields')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('form_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('model_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('auto_enable')->defaultTrue()->end()
                        ->arrayNode('show_fields')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('fullName', 'username', 'email', 'roles', 'enabled'))
                        ->end()
                        ->arrayNode('grid_columns')
                            ->defaultValue(array('fullName', 'username', 'email', 'roles', 'enabled'))
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('filters')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('fullName', 'username'))
                        ->end()
                        ->scalarNode('password_form')->defaultValue('symfonyid.admin.form.change_password_form')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildThemeConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('themes')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('dashboard')->defaultValue(Constants::TEMPLATE_DASHBOARD)->end()
                        ->scalarNode('profile')->defaultValue(Constants::TEMPLATE_PROFILE)->end()
                        ->scalarNode('change_password')->defaultValue(Constants::TEMPLATE_CHANGE_PASSWORD)->end()
                        ->scalarNode('new_view')->defaultValue(Constants::TEMPLATE_CREATE)->end()
                        ->scalarNode('bulk_new_view')->defaultValue(Constants::TEMPLATE_BULK_CREATE)->end()
                        ->scalarNode('edit_view')->defaultValue(Constants::TEMPLATE_EDIT)->end()
                        ->scalarNode('show_view')->defaultValue(Constants::TEMPLATE_SHOW)->end()
                        ->scalarNode('list_view')->defaultValue(Constants::TEMPLATE_LIST)->end()
                        ->scalarNode('pagination')->defaultValue(Constants::TEMPLATE_PAGINATION)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
