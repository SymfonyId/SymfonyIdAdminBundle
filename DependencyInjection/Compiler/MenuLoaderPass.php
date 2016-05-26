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
class MenuLoaderPass implements CompilerPassInterface
{
    const MENU_LOADER_FACTORY = 'symfonyid.admin.menu.menu_loader_factory';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::MENU_LOADER_FACTORY)) {
            return;
        }

        /*
         * Add all service with tag name symfonyid.extractor
         */
        $definition = $container->findDefinition(self::MENU_LOADER_FACTORY);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.menu');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addMenuLoader', array(new Reference($id)));
        }
    }
}
