<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Document\Filter;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use SymfonyId\AdminBundle\Annotation\Filter;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use SymfonyId\AdminBundle\Filter\FilterableTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends BsonFilter implements FieldsFilterInterface
{
    use FilterableTrait;

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
        $fields = array();
        $properties = $targetDocument->getReflectionProperties();
        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $this->extractorFactory->extract($property);
            foreach ($this->extractorFactory->getPropertyAnnotations() as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        /** @var GridConfigurator $grid */
        $grid = $this->configurationFactory->getConfigurator(GridConfigurator::class);
        $fields = !empty($fields) ? $fields : $grid->getFilter();

        foreach ($fields as $key => $field) {
            $fields[$key] = $targetDocument->getFieldMapping($field);
        }

        $output = array();
        foreach ($fields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $this->getParameter('filter'));
                if ($date) {
                    $output[$field['fieldName']] = $date->format($this->dateTimeFormat);
                }
            } else {
                $output[$field['fieldName']] = new \MongoRegex(sprintf('/.*%s.*/i', $this->getParameter('filter')));
            }
        }

        return $output;
    }
}
