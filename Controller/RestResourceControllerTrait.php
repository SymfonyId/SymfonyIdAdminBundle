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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Request\RequestParameter;

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
    public function getCollection(Request $request)
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

        $requestParam = new RequestParameter($request);

        /*
         * Convert from KnpPaginator to Pagerfanta.
         */
        /** @var \Knp\Component\Pager\Pagination\AbstractPagination $knpPaginator */
        $knpPaginator = $crudOperationHandler->paginateResult($driver, $reflectionModel->getName(), $requestParam->getPage(), $requestParam->getLimit());
        $pagerAdapter = new ArrayAdapter($knpPaginator->getItems());

        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($requestParam->getPage());
        $pager->setMaxPerPage($requestParam->getLimit());

        $embed = strtolower($reflectionModel->getShortName().'s');
        $pagerFactory = new PagerfantaFactory();
        $representation = $pagerFactory->createRepresentation(
            $pager,
            new Route($request->get('_route'), $requestParam->toArray()),
            new CollectionRepresentation($pager->getCurrentPageResults(), $embed, $embed)
        );

        return $this->handleView(new View($representation));
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function getNormalizedForm(FormInterface $form)
    {
        /** @var \SymfonyId\AdminBundle\Form\FormNormalizer $formNormalizer */
        $formNormalizer = $this->get('symfonyid.admin.form.form_normalizer');

        return $formNormalizer->normalize($form);
    }

    /**
     * @param View $view
     */
    protected function checkDepth(View $view)
    {
        $context = $view->getSerializationContext();
        $context->enableMaxDepthChecks();
        $view->setSerializationContext($context);
    }
}
