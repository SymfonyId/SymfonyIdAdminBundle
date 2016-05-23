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

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class QueryFilterPass implements CompilerPassInterface
{
    const ORM_CONFIGURATION = 'doctrine.orm.default_configuration';
    const ODM_CONFIGURATION = 'doctrine_mongodb.odm.default_configuration';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::ORM_CONFIGURATION) || !$container->has(self::ODM_CONFIGURATION)) {
            return;
        }

        /*
         * Add all service with tag name symfonyid.orm.filter
         */
        $definition = $container->findDefinition(self::ORM_CONFIGURATION);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.orm.filter');
        foreach ($taggedServices as $id => $tags) {
            $filter = $container->findDefinition($id);
            $definition->addMethodCall('addFilter', array($id, $filter->getClass()));
        }

        /*
         * Add all service with tag name symfonyid.odm.filter
         */
        $definition = $container->findDefinition(self::ODM_CONFIGURATION);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.odm.filter');
        foreach ($taggedServices as $id => $tags) {
            $filter = $container->findDefinition($id);
            $definition->addMethodCall('addFilter', array($id, $filter->getClass()));
        }
    }
}
