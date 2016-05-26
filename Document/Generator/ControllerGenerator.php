<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Document\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use SymfonyId\AdminBundle\Exception\RuntimeException;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerGenerator extends AbstractGenerator
{
    /**
     * Generates the entity form class.
     *
     * @param BundleInterface $bundle         The bundle in which to create the class
     * @param string          $entity         The entity relative class name
     * @param ClassMetadata   $metadata       The entity metadata class
     * @param bool            $forceOverwrite If true, remove any existing form class before generating it again
     *
     * @throws RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadata $metadata, $forceOverwrite = false)
    {
        /* @var \Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo $metadata */
        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'Controller';
        $dirPath = $bundle->getPath().'/Admin';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entityClass).'Controller.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('Controller.php.twig', $this->classPath, array(
            'namespace' => $bundle->getNamespace(),
            'fields' => $this->getFieldsFromMetadata($metadata),
            'entity' => $entity,
            'entity_class' => strtolower($entityClass),
            'title' => ucwords($entityClass),
            'form_class' => str_replace('Entity', 'Form', $entity),
            'controller_class' => $this->className,
        ));
    }
}
