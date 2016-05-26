<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultConfigurationPass implements CompilerPassInterface
{
    const DEFAULT_CONFIGURATON_FACTORY = 'symfonyid.admin.cache.default_configuration_factory';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::DEFAULT_CONFIGURATON_FACTORY)) {
            return;
        }

        /*
         * Add all service with tag name symfonyid.extractor
         */
        $definition = $container->findDefinition(self::DEFAULT_CONFIGURATON_FACTORY);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.default');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addDefaultConfiguration', array(new Reference($id)));
        }

        $definition->addMethodCall('freeze');
    }
}
