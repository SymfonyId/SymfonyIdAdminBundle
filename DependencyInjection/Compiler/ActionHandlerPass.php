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
class ActionHandlerPass implements CompilerPassInterface
{
    const CRUD_FACTORY = 'symfonyid.admin.crud.crud_factory';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::CRUD_FACTORY)) {
            return;
        }

        /*
         * Add all service with tag name siab.config
         */
        $definition = $container->findDefinition(self::CRUD_FACTORY);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.crud_action');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addActionHandler', array(new Reference($id)));
        }
    }
}
