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
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Controller\UserController;
use SymfonyId\AdminBundle\Extractor\Extractor;
use SymfonyId\AdminBundle\Filter\FieldsFilterInterface;
use SymfonyId\AdminBundle\Manager\DriverFinder;
use SymfonyId\AdminBundle\Manager\ManagerFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsFilterListener implements ConfigurationAwareInterface
{
    use CrudControllerListenerAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var Extractor
     */
    private $extractorFactory;

    /**
     * @var DriverFinder
     */
    private $driverFinder;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * @param ManagerFactory $managerFactory
     * @param Extractor      $extractorFactory
     * @param DriverFinder   $driverFinder
     * @param string         $dateTimeFormat
     */
    public function __construct(ManagerFactory $managerFactory, Extractor $extractorFactory, DriverFinder $driverFinder, $dateTimeFormat)
    {
        $this->managerFactory = $managerFactory;
        $this->extractorFactory = $extractorFactory;
        $this->driverFinder = $driverFinder;
        $this->dateTimeFormat = $dateTimeFormat;
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
        if (!$keyword = $request->query->get('filter')) {
            return;
        }

        $configurationFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configurationFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->driverFinder->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $manager = $this->managerFactory->getManager($driver);

        /* @var FieldsFilterInterface $filter */
        $filter = $manager->getFilters()->enable('symfonyid.admin.filter.'.$driver->getDriver().'.fields');
        $this->applyFilter($configurationFactory, $filter, $keyword);
    }

    /**
     * @param ConfiguratorFactory   $configuratorFactory
     * @param FieldsFilterInterface $filter
     * @param string                $keyword
     */
    private function applyFilter(ConfiguratorFactory $configuratorFactory, FieldsFilterInterface $filter, $keyword)
    {
        if ($this->controller instanceof UserController) {
            $filter->setFieldsFilter($this->container->getParameter('symfonyid.admin.user.grid_filters'));
        } else {
            $filter->setFieldsFilter($this->container->getParameter('symfonyid.admin.filters'));
        }

        $filter->setExtractorFactory($this->extractorFactory);
        $filter->setConfigurationFactory($configuratorFactory);
        $filter->setDateTimeFormat($this->dateTimeFormat);
        $filter->setParameter('filter', $keyword);
    }
}
