<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Generator;

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
class FormGenerator extends AbstractGenerator
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
        /* @var \Doctrine\ORM\Mapping\ClassMetadataInfo|\Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo $metadata */
        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'Type';
        $dirPath = $bundle->getPath().'/Form/Type';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'Type.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('FormType.php.twig', $this->classPath, array(
            'fields' => $this->getFieldsFromMetadata($metadata),
            'fields_mapping' => $metadata->fieldMappings,
            'namespace' => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'has_datetime_field' => $this->hasDateTimeField($metadata),
            'data_class' => $metadata->getReflectionClass()->getName(),
            'entity_class' => $entityClass,
            'bundle' => $bundle->getName(),
            'form_class' => $this->className,
            'form_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.substr($this->className, 0, -4)),
        ));
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return bool
     */
    private function hasDateTimeField(ClassMetadata $metadata)
    {
        /* @var \Doctrine\ORM\Mapping\ClassMetadataInfo|\Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo $metadata */
        $hasDateTime = false;

        foreach ($this->getFieldsFromMetadata($metadata) as $field) {
            if (array_key_exists($field, $metadata->fieldMappings)) {
                if (in_array($metadata->fieldMappings[$field]['type'], array('date', 'time', 'datetime'))) {
                    $hasDateTime = true;

                    break;
                }
            } else {
                if (in_array($metadata->associationMappings[$field]['type'], array('date', 'time', 'datetime'))) {
                    $hasDateTime = true;

                    break;
                }
            }
        }

        return $hasDateTime;
    }
}
