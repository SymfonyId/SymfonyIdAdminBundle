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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Event\FilterQueryEvent;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldSorterListener implements CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
    use ConfigurationAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var DriverFinder
     */
    private $driverFinder;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @param Session      $session
     * @param DriverFinder $driverFinder
     */
    public function __construct(Session $session, DriverFinder $driverFinder)
    {
        $this->session = $session;
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

        $request = $event->getRequest();
        if (!$this->sortBy = $request->query->get('sort_by')) {
            return;
        }
    }

    /**
     * @param FilterQueryEvent $event
     */
    public function onFilterQuery(FilterQueryEvent $event)
    {
        if (!$this->sortBy) {
            return;
        }

        if (!$this->sortBy) {
            $this->session->set(Constants::SESSION_SORTED_ID, null);

            return;
        }
        $this->session->set(Constants::SESSION_SORTED_ID, $this->sortBy);

        $configuratorFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        if (Driver::ORM === $driver->getDriver()) {
            /** @var \SymfonyId\AdminBundle\Doctrine\Filter\FieldSortFilter $filter */
            $filter = $this->container->get('symfonyid.admin.filter.orm.sort');
            $filter->sort($event->getModelClass(), $event->getQueryBuilder(), $this->sortBy);
        }

        if (Driver::ODM === $driver->getDriver()) {
            /** @var \SymfonyId\AdminBundle\Document\Filter\FieldSortFilter $filter */
            $filter = $this->container->get('symfonyid.admin.filter.odm.sort');
            $filter->sort($event->getModelClass(), $event->getQueryBuilder(), $this->sortBy);
        }
    }
}
