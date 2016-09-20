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
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareInterface;
use SymfonyId\AdminBundle\Controller\CrudControllerEventAwareTrait;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\Manager\ManagerFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SoftDeleteSubscriber implements ConfigurationAwareInterface, CrudControllerEventAwareInterface, EventSubscriberInterface
{
    use CrudControllerEventAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var DriverFinder
     */
    private $driverFinder;

    public function __construct(ManagerFactory $managerFactory, DriverFinder $driverFinder)
    {
        $this->managerFactory = $managerFactory;
        $this->driverFinder = $driverFinder;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function enableSoftDelete(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event) || !$event->isMasterRequest()) {
            return;
        }

        $configurationFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configurationFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $manager = $this->managerFactory->getManager($driver);

        /* @var \SymfonyId\AdminBundle\Filter\FieldSortInterface $filter */
        $filter = $manager->getFilters()->enable('symfonyid.admin.filter.'.$driver->getDriver().'.soft_deletable');
        $filter->setParameter('isDeleted', false);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('enableSoftDelete', -127),
            ),
        );
    }
}
