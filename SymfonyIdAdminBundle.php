<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\ActionHandlerPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\ConfiguratorPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\DefaultConfigurationPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\ExtractorPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\ManagerPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\MenuLoaderPass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\PaginationTemplatePass;
use SymfonyId\AdminBundle\DependencyInjection\Compiler\QueryFilterPass;
use SymfonyId\AdminBundle\DependencyInjection\SymfonyIdAdminExtension;
use SymfonyId\AdminBundle\User\User;

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

        $container->addCompilerPass(new ActionHandlerPass());
        $container->addCompilerPass(new ConfiguratorPass());
        $container->addCompilerPass(new DefaultConfigurationPass());
        $container->addCompilerPass(new ExtractorPass());
        $container->addCompilerPass(new ManagerPass());
        $container->addCompilerPass(new MenuLoaderPass());
        $container->addCompilerPass(new PaginationTemplatePass());
        $container->addCompilerPass(new QueryFilterPass());

        $this->addRegisterMappingsPass($container);
    }

    /**
     * @return SymfonyIdAdminExtension
     */
    public function getContainerExtension()
    {
        return new SymfonyIdAdminExtension();
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $reflectionUser = new \ReflectionClass(User::class);
        $reflectionBundle = new \ReflectionObject($this);

        $mappings = array(
            realpath(sprintf('%s/%s', dirname($reflectionBundle->getFileName()), 'Resources/config/doctrine')) => $reflectionUser->getNamespaceName(),
        );

        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver(
                $mappings,
                array('symfonyid.admin.manager.orm_manager'),
                'symfonyid.admin.backend_type_orm',
                array($reflectionBundle->getShortName() => $reflectionUser->getNamespaceName())
            ));
        }

        if (class_exists(DoctrineMongoDBMappingsPass::class)) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver(
                $mappings,
                array('symfonyid.admin.manager.odm_manager'),
                'symfonyid.admin.backend_type_odm',
                array($reflectionBundle->getShortName() => $reflectionUser->getNamespaceName())
            ));
        }
    }
}
