<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Document\Filter;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterAwareTrait;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as Metadata;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends BsonFilter implements FieldsFilterInterface
{
    use FieldsFilterAwareTrait;

    /**
     * Gets the criteria array to add to a query.
     *
     * If there is no criteria for the class, an empty array should be returned.
     *
     * @param ClassMetadata $targetDocument
     *
     * @return array
     */
    public function addFilterCriteria(ClassMetadata $targetDocument)
    {
        $this->filter($targetDocument, null);
    }

    public function filter(Metadata $metadata, $alias)
    {
        $output = array();

        /** @var ClassMetadata $metadata */
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $fields = array_merge($this->fieldsFilter, $gridConfigurator->getFilters($metadata->getReflectionClass()));

        /** @var CrudConfigurator $crudConfiguration */
        $crudConfiguration = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        if ($crudConfiguration->getCrud()->getModelClass() !== $metadata->getName()) {
            return $output;
        }

        $fixFields = array();
        foreach ($fields as $key => $field) {
            if ($metadata->hasField($field)) {
                $fixFields[] = $metadata->getFieldMapping($field);
            }
        }

        foreach ($fixFields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $this->getParameter('filter'));
                if ($date) {
                    $output[$field['fieldName']] = $date->format('Y-m-d');
                }
            } else {
                $output[$field['fieldName']] = new \MongoRegex(sprintf('/.*%s.*/i', $this->getParameter('filter')));
            }
        }

        return $output;
    }
}
