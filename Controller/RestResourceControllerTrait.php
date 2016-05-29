<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait RestResourceControllerTrait
{
    /**
     * Get controller class name.
     *
     * @return string
     */
    abstract protected function getClassName();

    /**
     * @param $serviceId
     *
     * @return object
     */
    abstract protected function get($serviceId);

    /**
     * @param $parameter
     *
     * @return object
     */
    abstract protected function getParameter($parameter);

    /**
     * @param View $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract protected function handleView(View $view);

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $reflectionModel = new \ReflectionClass($crudConfigurator->getCrud()->getModelClass());

        /** @var \SymfonyId\AdminBundle\Annotation\Driver $driver */
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($reflectionModel->getName());
        /** @var \SymfonyId\AdminBundle\Crud\CrudOperationHandler $crudOperationHandler */
        $crudOperationHandler = $this->get('symfonyid.admin.crud.crud_operation_handler');

        $params = $this->getRequestParam($request);

        /*
         * Convert from KnpPaginator to Pagerfanta.
         */
        /** @var \Knp\Component\Pager\Pagination\AbstractPagination $knpPaginator */
        $knpPaginator = $crudOperationHandler->paginateResult($driver, $reflectionModel->getName(), $params['page'], $params['limit']);
        $pagerAdapter = new ArrayAdapter($knpPaginator->getItems());

        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($params['page']);
        $pager->setMaxPerPage($params['limit']);

        $embed = strtolower($reflectionModel->getShortName().'s');
        $pagerFactory = new PagerfantaFactory();
        $representation = $pagerFactory->createRepresentation(
            $pager,
            new Route($request->get('_route'), $params),
            new CollectionRepresentation($pager->getCurrentPageResults(), $embed, $embed)
        );

        return $this->handleView(new View($representation));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getRequestParam(Request $request)
    {
        $params = array(
            'page' => $request->query->get('page', 1),
            'limit' => $request->query->get('limit', $this->getParameter('symfonyid.admin.per_page')),
        );

        if ($filter = $request->query->get('q')) {
            $params['q'] = $filter;
        }

        if ($sortBy = $request->query->get('sort_by')) {
            $params['sort_by'] = $sortBy;
        }

        return $params;
    }
}
