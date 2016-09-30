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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareInterface;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareTrait;
use SymfonyId\AdminBundle\Doctrine\Filter\JoinFieldFilter;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class JoinFieldFilterSubscriber implements CrudControllerEventAwareInterface , EventSubscriberInterface
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
     * @var JoinFieldFilter
     */
    private $joinFieldFilter;

    /**
     * @var bool
     */
    private $validListener;

    /**
     * @var string
     */
    private $keyword;

    /**
     * @param EntityManager $entityManager
     * @param DriverFinder  $driverFinder
     * @param JoinFieldFilter $joinFieldFilter
     */
    public function __construct(EntityManager $entityManager, DriverFinder $driverFinder, JoinFieldFilter $joinFieldFilter)
    {
        $this->entityManager = $entityManager;
        $this->driverFinder = $driverFinder;
        $this->joinFieldFilter = $joinFieldFilter;
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
            $this->joinFieldFilter->setKeyword($this->keyword);

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

        /**
         * TODO: Add ODM support
         */
        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        if (Driver::ORM !== $driver->getDriver()) {
            return;
        }
        $this->entityManager->getFilters()->disable(sprintf('symfonyid.admin.filter.%s.fields', $driver->getDriver()));
        $metadata = $this->entityManager->getClassMetadata($event->getModelClass());

        $this->joinFieldFilter->filter($metadata, Constants::MODEL_ALIAS);
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
}
