<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\Manager\ManagerFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableSoftDeleteListener implements ConfigurationAwareInterface, CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
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
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $configurationFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configurationFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $manager = $this->managerFactory->getManager($driver);

        if (Driver::ORM === $driver->getDriver()) {
            /* @var \Doctrine\ORM\EntityManager $manager */
            /* @var \SymfonyId\AdminBundle\Filter\FieldSortInterface $filter */
            $filter = $manager->getFilters()->enable('symfonyid.admin.filter.orm.soft_deletable');
            $filter->setParameter('isDeleted', false);
        }

        if (Driver::ODM === $driver->getDriver()) {
            /* @var \Doctrine\ODM\MongoDB\DocumentManager $manager */
            /* @var \SymfonyId\AdminBundle\Filter\FieldSortInterface $filter */
            $filter = $manager->getFilterCollection()->enable('symfonyid.admin.filter.odm.soft_deletable');
            $filter->setParameter('isDeleted', false);
        }
    }
}
