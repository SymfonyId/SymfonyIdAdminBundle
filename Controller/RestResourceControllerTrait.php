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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SymfonyId\AdminBundle\Annotation\Serialize;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\SecurityConfigurator;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Request\RequestParameter;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait RestResourceControllerTrait
{
    /**
     * @var Serialize
     */
    protected $serialization;

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
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, FormInterface $form)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        $modelClass = $crudConfigurator->getCrud()->getModelClass();
        $form->setData(new $modelClass());

        return $this->save($request, $form, $crudConfigurator);
    }

    /**
     * @param FormInterface $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCreateForm(FormInterface $form)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        return $this->createResponse($this->getNormalizedForm($form));
    }

    /**
     * @param Request       $request
     * @param FormInterface $form
     * @param int           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, FormInterface $form, $id)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_UPDATE);

        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);
        $form->setData($model);

        return $this->save($request, $form, $crudConfigurator);
    }

    /**
     * @param FormInterface $form
     * @param int           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUpdateForm(FormInterface $form, $id)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_UPDATE);

        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);
        $form->setData($model);

        return $this->createResponse($this->getNormalizedForm($form));
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remove($id)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_DELETE);

        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);

        $reflectionModel = new \ReflectionClass($crudConfigurator->getCrud()->getModelClass());
        /** @var \SymfonyId\AdminBundle\Annotation\Driver $driver */
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($reflectionModel->getName());
        /** @var \SymfonyId\AdminBundle\Crud\CrudOperationHandler $crudOperationHandler */
        $crudOperationHandler = $this->get('symfonyid.admin.crud.crud_operation_handler');

        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        if (false === $crudOperationHandler->remove($driver, $model)) {
            return $this->handleView(new View(array(
                'status' => false,
                'message' => $translator->trans($crudOperationHandler->getErrorMessage(), array(), $translationDomain),
            )));
        }

        return $this->handleView(new View(array(
            'status' => true,
            'message' => $translator->trans('message.data_deleted', array('%id%' => $id), $translationDomain),
        )));
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSingle($id)
    {
        /** @var \SymfonyId\AdminBundle\Configuration\ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_READ);

        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);

        return $this->createResponse($model);
    }

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

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_READ);

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

        return $this->createResponse($representation);
    }

    /**
     * @param Serialize $serialize
     */
    public function setSerialization(Serialize $serialize)
    {
        $this->serialization = $serialize;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getNormalizedForm(FormInterface $form)
    {
        /** @var \SymfonyId\AdminBundle\Form\FormNormalizer $formNormalizer */
        $formNormalizer = $this->get('symfonyid.admin.form.form_normalizer');

        return $formNormalizer->normalize($form);
    }

    /**
     * @param int $id
     *
     * @return ModelInterface
     */
    protected function findOr404Error($id)
    {
        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $modelClass = $crudConfigurator->getCrud()->getModelClass();
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($modelClass);
        $crudOperationHandler = $this->get('symfonyid.admin.crud.crud_operation_handler');

        /** @var ModelInterface $model */
        $model = $crudOperationHandler->find($driver, $modelClass, $id);
        if (!$model) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%' => $id), $translationDomain));
        }

        return $model;
    }

    /**
     * @param CrudConfigurator $crudConfigurator
     * @param string           $action
     *
     * @return bool
     */
    protected function isGrantedOr404Error(CrudConfigurator $crudConfigurator, $action)
    {
        $authorizationChecker = $this->get('symfonyid.admin.security.authorization_checker');
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var SecurityConfigurator $securityConfigurator */
        $securityConfigurator = $configuratorFactory->getConfigurator(SecurityConfigurator::class);

        return $authorizationChecker->isGrantedOr404Error($crudConfigurator, $action, $securityConfigurator->isGranted($action));
    }

    /**
     * @param mixed $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createResponse($data = null)
    {
        $view = new View();
        $view->setData($data);

        $context = $view->getSerializationContext();
        if ($this->serialization->isCheckDepth()) {
            $context->enableMaxDepthChecks();
        }
        $context->setGroups($this->serialization->getGroups());

        $view->setSerializationContext($context);

        return $this->handleView($view);
    }

    /**
     * @param View $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request          $request
     * @param FormInterface    $form
     * @param CrudConfigurator $crudConfigurator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function save(Request $request, FormInterface $form, CrudConfigurator $crudConfigurator)
    {
        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        $form->handleRequest($request);
        if (!$form->isValid()) {
            return $this->handleView(new View(array(
                'status' => false,
                'message' => $translator->trans('message.form_not_valid', array(), $translationDomain),
            )));
        }

        $reflectionModel = new \ReflectionClass($crudConfigurator->getCrud()->getModelClass());
        /** @var \SymfonyId\AdminBundle\Annotation\Driver $driver */
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($reflectionModel->getName());
        /** @var \SymfonyId\AdminBundle\Crud\CrudOperationHandler $crudOperationHandler */
        $crudOperationHandler = $this->get('symfonyid.admin.crud.crud_operation_handler');

        $model = $form->getData();
        if (!$crudOperationHandler->save($driver, $model)) {
            return $this->handleView(new View(array(
                'status' => false,
                'message' => $translator->trans($crudOperationHandler->getErrorMessage(), array(), $translationDomain),
            )));
        }

        return $this->createResponse($model);
    }
}
