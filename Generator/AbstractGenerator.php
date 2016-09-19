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
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractGenerator extends Generator implements GeneratorInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $classPath;

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadata $metadata
     *
     * @return array $fields
     */
    public function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        /* @var \Doctrine\ORM\Mapping\ClassMetadataInfo|\Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo $metadata */
        $fields = (array) $metadata->getFieldNames();

        $exclude = array(
            'createdAt', 'created_at', 'createdBy', 'created_by',
            'updatedAt', 'updated_at', 'updatedBy', 'updated_by',
        );

        // Remove the primary key field if it's not managed manually
        if (method_exists($metadata, 'isIdentifierNatural') && !$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== 4) {
                $fields[] = $fieldName;
            }
        }

        return array_values(array_diff($fields, $exclude));
    }

    /**
     * @param string $template
     * @param string $target
     * @param array  $parameters
     *
     * @return int
     */
    protected function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($template, $parameters));
    }
}
