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
use Doctrine\ODM\MongoDB\Query\Builder;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterAwareTrait;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as Metadata;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter implements FieldsFilterInterface
{
    use FieldsFilterAwareTrait;

    /**
     * @var Builder
     */
    private $queryBuilder;

    /**
     * @var string
     */
    private $keyword;

    public function setQueryBuilder(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @param string $key
     * @param string $param
     */
    public function setParameter($key, $param)
    {
        throw new \InvalidArgumentException(sprintf('Method %s not used. Use setKeyword instead.', __METHOD__));
    }

    public function filter(Metadata $metadata, $alias)
    {
        /** @var ClassMetadata $metadata */
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $fields = array_merge($this->fieldsFilter, $gridConfigurator->getFilters($metadata->getReflectionClass()));

        /** @var CrudConfigurator $crudConfiguration */
        $crudConfiguration = $this->configuratorFactory->getConfigurator(CrudConfigurator::class);
        if ($crudConfiguration->getCrud()->getModelClass() !== $metadata->getName()) {
            return;
        }

        $fixFields = array();
        foreach ($fields as $key => $field) {
            if ($metadata->hasField($field)) {
                $fixFields[] = $metadata->getFieldMapping($field);
            }
        }

        foreach ($fixFields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $this->keyword);
                if ($date) {
                    $this->queryBuilder->field($field['fieldName'])->equals(new \MongoRegex(sprintf('/.*%s.*/i', $date->format('Y-m-d'))));
                }
            } else {
                $this->queryBuilder->field($field['fieldName'])->equals(new \MongoRegex(sprintf('/.*%s.*/i', $this->keyword)));
            }
        }
    }
}
