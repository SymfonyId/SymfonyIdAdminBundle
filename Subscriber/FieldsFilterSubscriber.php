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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Controller\AnnotationConfigurationAwareInterface;
use SymfonyId\AdminBundle\Controller\AnnotationConfigurationAwareTrait;
use SymfonyId\AdminBundle\Doctrine\Filter\FieldsFilter as OrmFilter;
use SymfonyId\AdminBundle\Document\Filter\FieldsFilter as OdmFilter;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\Manager\ManagerFactory;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilterSubscriber implements AnnotationConfigurationAwareInterface , EventSubscriberInterface
{
    use AnnotationConfigurationAwareTrait;

    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var DriverFinder
     */
    private $driverFinder;

    /**
     * @var OrmFilter
     */
    private $ormFilter;

    /**
     * @var OdmFilter
     */
    private $odmFilter;

    /**
     * @var bool
     */
    private $validListener;

    /**
     * @var string
     */
    private $keyword;

    /**
     * @param ManagerFactory $managerFactory
     * @param DriverFinder   $driverFinder
     * @param OrmFilter      $ormFilter
     * @param OdmFilter      $odmFilter
     */
    public function __construct(ManagerFactory $managerFactory, DriverFinder $driverFinder, OrmFilter $ormFilter, OdmFilter $odmFilter)
    {
        $this->managerFactory = $managerFactory;
        $this->driverFinder = $driverFinder;
        $this->ormFilter = $ormFilter;
        $this->odmFilter = $odmFilter;
        $this->validListener = false;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function checkValidListener(FilterControllerEvent $event)
    {
        if (!$this->isValidListener($event) || !$event->isMasterRequest()) {
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

        $driver = $this->driverFinder->findDriverForClass($event->getModelClass());
        $this->managerFactory->setModelClass($event->getModelClass());
        $metadata = $this->managerFactory->getManager($driver)->getClassMetadata();
        if (Driver::ORM === $driver->getDriver()) {
            $this->ormFilter->setKeyword($this->keyword);
            $this->ormFilter->setQueryBuilder($event->getQueryBuilder());
            $this->ormFilter->filter($metadata, $event->getAlias());
        } else {
            $this->odmFilter->setKeyword($this->keyword);
            $this->odmFilter->setQueryBuilder($event->getQueryBuilder());
            $this->odmFilter->filter($metadata, $event->getAlias());
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
}
