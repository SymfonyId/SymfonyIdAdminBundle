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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Filter\FieldsFilterAwareTrait;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class JoinFieldFilter implements FieldsFilterInterface
{
    use FieldsFilterAwareTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $aliases = array();

    /**
     * @var string
     */
    private $keyword;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param string $keyword
     */
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

    /**
     * @param ClassMetadata $metadata
     * @param string $alias
     */
    public function filter(ClassMetadata $metadata, $alias)
    {
        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $fields = $this->getFieldFilter($metadata, $gridConfigurator->getFilters($metadata->getReflectionClass()) ?: $this->fieldsFilter);

        foreach ($fields as $key => $field) {
            if (array_key_exists('join', $field)) {
                $this->queryBuilder->leftJoin(sprintf('%s.%s', Constants::MODEL_ALIAS, $field['join_field']), $field['join_alias'], 'WITH');
                $this->buildFilter($this->queryBuilder, $field, $field['join_alias'], $key, $this->keyword);
            } else {
                $this->buildFilter($this->queryBuilder, $field, Constants::MODEL_ALIAS, $key, $this->keyword);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $metadata
     * @param string $alias
     * @param string $parameter
     * @param string $filter
     */
    private function buildFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $parameter, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->dateTimeFormat, $filter);
            if ($date) {
                $queryBuilder->orWhere(sprintf('%s.%s = ?%d', $alias, $metadata['fieldName'], $parameter));
                $queryBuilder->setParameter($parameter, $date->format('Y-m-d'));
            }
        } else {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', $alias, $metadata['fieldName'], $parameter));
            $queryBuilder->setParameter($parameter, strtr('%filter%', array('filter' => $filter)));
        }
    }

    /**
     * @param ClassMetadata $metadata
     * @param array         $fields
     *
     * @return array
     */
    private function getFieldFilter(ClassMetadata $metadata, array $fields)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $filters = array();
        foreach ($fields as $field) {
            $fieldName = $this->getFieldName($metadata, $field);
            try {
                $filters[] = $metadata->getFieldMapping($fieldName);
            } catch (\Exception $ex) {
                /**
                 * TODO: Use @Filter on relation entity
                 */
                $mapping = $metadata->getAssociationMapping($fieldName);
                $associationMatadata = $this->entityManager->getClassMetadata($mapping['targetEntity']);
                $associationFields = $associationMatadata->getFieldNames();
                $associationIdentifier = $associationMatadata->getIdentifierFieldNames();
                $associationFields = array_values(array_filter(
                    $associationFields,
                    function ($value) use ($associationIdentifier) {
                        return !in_array($value, $associationIdentifier);
                    }
                ));
                if ($associationFields) {
                    $filters[] = array_merge(array(
                        'join' => true,
                        'join_field' => $fieldName,
                        'join_alias' => $this->getAlias(),
                    ), $associationMatadata->getFieldMapping($associationFields[0]));
                }
            }
        }

        return $filters;
    }

    /**
     * @param ClassMetadata $metadata
     * @param string        $field
     *
     * @return string
     */
    private function getFieldName(ClassMetadata $metadata, $field)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        return $metadata->getFieldName($field) ?: $metadata->getFieldForColumn($field);
    }

    /**
     * @return string
     */
    private function getAlias()
    {
        $alias = uniqid('ad3n');
        if (in_array($alias, $this->aliases)) {
            $alias = $this->getAlias();
        }
        $this->aliases = $alias;

        return $alias;
    }
}
