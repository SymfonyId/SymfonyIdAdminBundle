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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SymfonyId\AdminBundle\Annotation\AutoComplete;
use SymfonyId\AdminBundle\Annotation\DatePicker;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Annotation\ExternalJavascript;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\GridConfigurator;
use SymfonyId\AdminBundle\Configuration\PageConfigurator;
use SymfonyId\AdminBundle\Configuration\PluginConfigurator;
use SymfonyId\AdminBundle\Configuration\SecurityConfigurator;
use SymfonyId\AdminBundle\Configuration\UtilConfigurator;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class CrudController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
        $template = $pluginConfigurator->isBulkInsertEnabled() ? $crudConfigurator->getTemplate()->getBulkCreate() : $crudConfigurator->getTemplate()->getCreate();

        $modelClass = $crudConfigurator->getCrud()->getModelClass();
        $model = new $modelClass();
        $form = $crudConfigurator->getForm($model);

        return $this->createOrUpdate($request, $model, $form, Constants::ACTION_CREATE, $template);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkNewAction(Request $request)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);

        if (!$pluginConfigurator->isBulkInsertEnabled()) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.request_not_found', array(), $this->getParameter('symfonyid.admin.translation_domain')));
        }

        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setView($this->get('symfonyid.admin.view.view'));
        $crudFactory->setDriver($driver);
        $crudFactory->setRequest($request);

        return $crudFactory->bulkCreate($crudConfigurator);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_UPDATE);

        $model = $this->findOr404Error($id);
        $form = $crudConfigurator->getForm($model);

        return $this->createOrUpdate($request, $model, $form, Constants::ACTION_UPDATE, $crudConfigurator->getTemplate()->getEdit());
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        /* @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
        /* @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_READ);
        $crud = $crudConfigurator->getCrud();

        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');
        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);
        /** @var View $view */
        $view = $this->get('symfonyid.admin.view.view');
        $view->setParam('page_title', $translator->trans($pageConfigurator->getTitle(), array(), $translationDomain));
        $view->setParam('page_description', $translator->trans($pageConfigurator->getDescription(), array(), $translationDomain));

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crud->getModelClass());
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setRequest($request);
        $crudFactory->setDriver($driver);
        $crudFactory->setView($view);
        $crudFactory->setTemplate($crudConfigurator->getTemplate()->getShow());
        $showFields = $crud->getShowFields() ?: $gridConfigurator->getColumns(new \ReflectionClass($crud->getModelClass()));

        return $crudFactory->showDetail($model, $showFields, $crud->isAllowDelete());
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_DELETE);

        /** @var ModelInterface $model */
        $model = $this->findOr404Error($id);
        /** @var View $view */
        $view = $this->get('symfonyid.admin.view.view');

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setRequest($request);
        $crudFactory->setDriver($driver);
        $crudFactory->setView($view);

        return $crudFactory->remove($model);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setView($this->get('symfonyid.admin.view.view'));
        $crudFactory->setDriver($driver);
        $crudFactory->setRequest($request);

        return $crudFactory->bulkDelete($crudConfigurator->getCrud()->getModelClass());
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \SymfonyId\AdminBundle\Exception\CallMethodBeforeException
     */
    public function listAction(Request $request)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_READ);

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        /* @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);

        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        $template = $crudConfigurator->getTemplate();
        $listTemplate = $request->isXmlHttpRequest() ? $template->getAjaxTemplate() : $template->getList();

        $reflectionModel = new \ReflectionClass($crudConfigurator->getCrud()->getModelClass());
        $filters = $gridConfigurator->getFilters($reflectionModel);

        /** @var View $view */
        $view = $this->get('symfonyid.admin.view.view');
        $view->setParam('page_title', $translator->trans($pageConfigurator->getTitle(), array(), $translationDomain));
        $view->setParam('page_description', $translator->trans($pageConfigurator->getDescription(), array(), $translationDomain));

        /*
         * Translate tentity fields
         */
        $view->setParam('filter_fields', implode(', ', array_map(function ($value) use ($translator, $translationDomain) {
            return $translator->trans(sprintf('entity.fields.%s', $value), array(), $translationDomain);
        }, $filters)));
        $view->setParam('filter_fields_entity', implode(', ', $filters));

        return $this->doList($request, $crudConfigurator, $view, $gridConfigurator->getColumns($reflectionModel), $listTemplate, $gridConfigurator->getGrid()->isFormatNumber());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAction()
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $this->isGrantedOr404Error($crudConfigurator, Constants::ACTION_DOWNLOAD);

        $modelClass = $crudConfigurator->getCrud()->getModelClass();
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($modelClass);
        $this->isGrantedDownloadOr404Error($driver, $modelClass);

        $dataExporter = $this->get('symfonyid.admin.export.data_exporter');
        $dataExporter->setModelClass($modelClass);

        /** @var GridConfigurator $gridConfigurator */
        $gridConfigurator = $configuratorFactory->getConfigurator(GridConfigurator::class);
        $reflectionModel = new \ReflectionClass($modelClass);

        return $dataExporter->exportToExcel($driver, $gridConfigurator->getColumns($reflectionModel));
    }

    /**
     * @param Request        $request
     * @param ModelInterface $model
     * @param FormInterface  $form
     * @param string         $action
     * @param string         $template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \SymfonyId\AdminBundle\Exception\CallMethodBeforeException
     */
    private function createOrUpdate(Request $request, ModelInterface $model, FormInterface $form, $action, $template)
    {
        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /* @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
        /** @var UtilConfigurator $utilConfigurator */
        $utilConfigurator = $configuratorFactory->getConfigurator(UtilConfigurator::class);
        /** @var AutoComplete $autoComplete */
        $autoComplete = $utilConfigurator->getAutoComplete();
        /** @var DatePicker $datePicker */
        $datePicker = $utilConfigurator->getDatePicker();
        /** @var ExternalJavascript $externalJavascript */
        $externalJavascript = $utilConfigurator->getExternalJavascript();

        /** @var View $view */
        $view = $this->get('symfonyid.admin.view.view');
        $view->setParam('page_title', $translator->trans($pageConfigurator->getTitle(), array(), $translationDomain));
        $view->setParam('page_description', $translator->trans($pageConfigurator->getDescription(), array(), $translationDomain));
        $view->setParam('action_method', $translator->trans('page.'.strtolower($action), array(), $translationDomain));
        $view->setParam('use_file_style', $pluginConfigurator->isFileChooserEnabled());
        $view->setParam('use_editor', $pluginConfigurator->isHtmlEditorEnabled());
        $view->setParam('use_numeric', $pluginConfigurator->isNumericEnabled());
        $view->setParam('inline_form', $pluginConfigurator->isInlineFormEnabled());
        $view->setParam('autocomplete', false);
        $view->setParam('include_javascript', false);
        //Auto complete
        if ($autoComplete) {
            $view->setParam('autocomplete', true);
            $view->setParam('ac_config', array(
                'route' => $autoComplete->getRouteResource(),
                'route_callback' => $autoComplete->getRouteCallback(),
                'selector_storage' => $autoComplete->getTargetSelector(),
            ));
        }
        //Date picker
        if ($datePicker) {
            $view->setParam('use_date_picker', true);
            $view->setParam('date_picker', array(
                'date_format' => $datePicker->getDateFormat(),
                'flatten' => $datePicker->isFlatten(),
            ));
        }
        //External Javascript
        if ($externalJavascript) {
            $view->setParam('include_javascript', true);
            $view->setParam('js_include', array(
                'files' => $externalJavascript->getIncludFiles(),
                'route' => $externalJavascript->getIncludeRoutes(),
            ));
        }

        return $this->doCreateOrUpdate($model, $request, $view, $form, $template);
    }

    /**
     * @param ModelInterface $model
     * @param Request        $request
     * @param View           $view
     * @param FormInterface  $form
     * @param string         $template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function doCreateOrUpdate(ModelInterface $model, Request $request, View $view, FormInterface $form, $template)
    {
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass(get_class($model));
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setDriver($driver);
        $crudFactory->setRequest($request);
        $crudFactory->setTemplate($template);
        $crudFactory->setView($view);

        return $crudFactory->createOrUpdate($form);
    }

    /**
     * @param Request          $request
     * @param CrudConfigurator $crudConfigurator
     * @param View             $view
     * @param array            $columns
     * @param string           $template
     * @param bool             $formatNumber
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function doList(Request $request, CrudConfigurator $crudConfigurator, View $view, array $columns, $template, $formatNumber = true)
    {
        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $crudFactory = $this->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setDriver($driver);
        $crudFactory->setView($view);
        $crudFactory->setRequest($request);
        $crudFactory->setTemplate($template);

        return $crudFactory->listView($crudConfigurator, $columns, $this->isGrantedBulkDelete($crudConfigurator), $formatNumber);
    }

    /**
     * @param int $id
     *
     * @return ModelInterface
     */
    private function findOr404Error($id)
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
    private function isGrantedOr404Error(CrudConfigurator $crudConfigurator, $action)
    {
        $authorizationChecker = $this->get('symfonyid.admin.security.authorization_checker');
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var SecurityConfigurator $securityConfigurator */
        $securityConfigurator = $configuratorFactory->getConfigurator(SecurityConfigurator::class);

        return $authorizationChecker->isGrantedOr404Error($crudConfigurator, $action, $securityConfigurator->isGranted($action));
    }

    /**
     * @param Driver $driver
     * @param string $modelClass
     *
     * @return bool
     */
    private function isGrantedDownloadOr404Error(Driver $driver, $modelClass)
    {
        $dataExporter = $this->get('symfonyid.admin.export.data_exporter');
        $dataExporter->setModelClass($modelClass);
        $translator = $this->get('translator');
        $translationDomain = $this->getParameter('symfonyid.admin.translation_domain');

        if (!$dataExporter->isAllowExport($driver, $this->container->getParameter('symfonyid.admin.max_records'))) {
            throw new NotFoundHttpException($translator->trans('message.request_not_found', array(), $translationDomain));
        }

        return true;
    }

    /**
     * @param CrudConfigurator $crudConfigurator
     *
     * @return bool
     */
    private function isGrantedBulkDelete(CrudConfigurator $crudConfigurator)
    {
        $authorizationChecker = $this->get('symfonyid.admin.security.authorization_checker');

        return $authorizationChecker->isGrantedBulkDelete($crudConfigurator);
    }
}
