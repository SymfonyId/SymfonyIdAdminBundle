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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdAdminExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('annotation.xml');
        $loader->load('cache.xml');
        $loader->load('configuration.xml');
        $loader->load('crud.xml');
        $loader->load('event_listener.xml');
        $loader->load('extractors.xml');
        $loader->load('filters.xml');
        $loader->load('form.xml');
        $loader->load('listeners.xml');
        $loader->load('menu.xml');
        $loader->load('twig.xml');

        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $parameterBuilder = new ParameterBuilder($container);
        $parameterBuilder->build($config);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $reflection = new \ReflectionClass(Configuration::class);
        $container->addResource(new FileResource($reflection->getFileName()));

        return new Configuration();
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return Constants::CONFIGURATION_ALIAS;
    }
}
