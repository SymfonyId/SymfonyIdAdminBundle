<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use SymfonyId\AdminBundle\Filter\FieldsFilterAwareTrait;

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
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $fields = array();
        $properties = $targetEntity->getReflectionProperties();
        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $this->extractorFactory->extract($property);
            foreach ($this->extractorFactory->getPropertyAnnotations() as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $gridConfigurator->getFilters($targetEntity->getReflectionClass());

        foreach ($fields as $key => $field) {
            $fields[$key] = $targetEntity->getFieldMapping($targetEntity->getFieldName($field));
        }

        $filter = '';
        $parameter = str_replace('\'', '', $this->getParameter('filter'));//Remove single quote from paramter
        /*
         * Filter is low level query so you can't use property name as field filter, use column name instead
         */
        foreach ($fields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $parameter);
                if ($date) {
                    $filter .= sprintf('%s.%s = \'%s\' OR ', $targetTableAlias, $field['columnName'], $date->format($this->dateTimeFormat));
                }
            } else {
                $filter .= sprintf('%s.%s LIKE \'%%%s%%\' OR ', $targetTableAlias, $field['columnName'], $parameter);
            }
        }

        return rtrim($filter, ' OR ');
    }
}
