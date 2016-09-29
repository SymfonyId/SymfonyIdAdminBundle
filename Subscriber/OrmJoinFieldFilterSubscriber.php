<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Subscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareInterface;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareTrait;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class OrmJoinFieldFilterSubscriber implements CrudControllerEventAwareInterface , EventSubscriberInterface
{
    use CrudControllerEventAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DriverFinder
     */
    private $driverFinder;

    /**
     * @var bool
     */
    private $validListener;

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
     * @param DriverFinder  $driverFinder
     */
    public function __construct(EntityManager $entityManager, DriverFinder $driverFinder)
    {
        $this->entityManager = $entityManager;
        $this->driverFinder = $driverFinder;
        $this->validListener = false;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function checkValidListener(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event) || !$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->keyword = $request->query->get('filter')) {
            return;
        }

        $this->validListener = true;
    }

    /**
     * @param FilterQueryEvent $event
     */
    public function filter(FilterQueryEvent $event)
    {
        if (!$this->validListener) {
            return;
        }

        $configuratorFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        if (Driver::ORM !== $driver->getDriver()) {
            return;
        }
        $this->entityManager->getFilters()->disable(sprintf('symfonyid.admin.filter.%s.fields', $driver->getDriver()));
        $metadata = $this->entityManager->getClassMetadata($event->getModelClass());

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $this->configuratorFactory->getConfigurator(GridConfigurator::class);
        $fields = $this->getFieldFilter($metadata, $gridConfigurator->getFilters($metadata->getReflectionClass()));

        $queryBuilder = $event->getQueryBuilder();

        foreach ($fields as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::MODEL_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->buildFilter($queryBuilder, $value, $value['join_alias'], $key, $this->keyword);
            } else {
                $this->buildFilter($queryBuilder, $value, Constants::MODEL_ALIAS, $key, $this->keyword);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('checkValidListener', -127),
            ),
            Constants::FILTER_LIST => array(
                array('filter', 0),
            ),
        );
    }

    private function buildFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $parameter, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->container->getParameter('symfonyid.admin.date_time_format'), $filter);
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
        $filters = array();
        foreach ($fields as $field) {
            $fieldName = $this->getFieldName($metadata, $field);
            try {
                $filters[] = $metadata->getFieldMapping($fieldName);
            } catch (\Exception $ex) {
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
        return $metadata->getFieldName($field) ?: $metadata->getFieldForColumn($field);
    }

    /**
     * @return int
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
