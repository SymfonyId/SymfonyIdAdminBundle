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
class ExtractorPass implements CompilerPassInterface
{
    const EXTRACTOR = 'symfonyid.admin.extractor.extractor';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::EXTRACTOR)) {
            return;
        }

        /*
         * Add all service with tag name symfonyid.extractor
         */
        $definition = $container->findDefinition(self::EXTRACTOR);
        $taggedServices = $container->findTaggedServiceIds('symfonyid.extractor');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtractor', array(new Reference($id)));
        }

        $definition->addMethodCall('freeze');
    }
}
