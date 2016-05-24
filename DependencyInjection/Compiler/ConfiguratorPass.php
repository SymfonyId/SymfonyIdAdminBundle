<?php

/*
 * This file is part of the AdminBundle package.
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
class ConfiguratorPass implements CompilerPassInterface
{
    const CONFIGURATOR_FACTORY = 'symfonyid.admin.congiration.configurator_factory';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::CONFIGURATOR_FACTORY)) {
            return;
        }

        /*
         * Add all service with tag name siab.config
         */
        $definition = $container->findDefinition(self::CONFIGURATOR_FACTORY);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.config');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addConfiguration', array(new Reference($id)));
        }
    }
}
