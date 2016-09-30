<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterAwareTrait;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as Metadata;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends SQLFilter implements FieldsFilterInterface
{
    use FieldsFilterAwareTrait;

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $this->filter($targetEntity, $targetTableAlias);
    }

    /**
     * @param Metadata $metadata
     * @param string $alias
     * @return string
     */
    public function filter(Metadata $metadata, $alias)
    {
        $filter = '';

        /** @var ClassMetadata $metadata */
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $fields = $gridConfigurator->getFilters($metadata->getReflectionClass()) ?: $this->fieldsFilter;

        /** @var CrudConfigurator $crudConfiguration */
        $crudConfiguration = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        if ($crudConfiguration->getCrud()->getModelClass() !== $metadata->getName()) {
            return $filter;
        }

        $fixFields = array();
        foreach ($fields as $key => $field) {
            if ($metadata->hasField($field)) {
                $fixFields[] = $metadata->getFieldMapping($metadata->getFieldName($field));
            }
        }
        $parameter = str_replace('\'', '', $this->getParameter('filter')); //Remove single quote from paramter
        /*
         * Filter is low level query so you can't use property name as field filter, use column name instead
         */
        foreach ($fixFields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $parameter);
                if ($date) {
                    $filter .= sprintf('%s.%s = \'%s\' OR ', $alias, $field['columnName'], $date->format('Y-m-d'));
                }
            } else {
                $filter .= sprintf('%s.%s LIKE \'%%%s%%\' OR ', $alias, $field['columnName'], $parameter);
            }
        }

        return rtrim($filter, ' OR ');
    }
}
