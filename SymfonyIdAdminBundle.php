<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\PaginationTemplatePass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\QueryFilterPass;
use SymfonyId\AdminBundle\DependencyInjection\SymfonyIdAdminExtension;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PaginationTemplatePass());
        $container->addCompilerPass(new ConfigurationPass());
        $container->addCompilerPass(new ExtractorPass());
        $container->addCompilerPass(new QueryFilterPass());
    }

    /**
     * @return SymfonyIdAdminExtension
     */
    public function getContainerExtension()
    {
        return new SymfonyIdAdminExtension();
    }
}
