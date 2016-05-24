<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\AutoComplete;
use SymfonyId\AdminBundle\Annotation\DatePicker;
use SymfonyId\AdminBundle\Annotation\ExternalJavascript;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\PageConfigurator;
use SymfonyId\AdminBundle\Configuration\PluginConfigurator;
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

        $authorizationChecker = $this->container->get('symfonyid.admin.security.authorization_checker');
        $authorizationChecker->isAllowOr404Error($crudConfigurator, Constants::ACTION_CREATE);

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
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->container->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());
        $crudFactory = $this->container->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setDriver($driver);
        $crudFactory->setRequest($request);
        $crudFactory->bulkCreate($crudConfigurator);

        return $crudFactory->bulkCreate($crudConfigurator);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $authorizationChecker = $this->container->get('symfonyid.admin.security.authorization_checker');
        $authorizationChecker->isAllowOr404Error($crudConfigurator, Constants::ACTION_CREATE);

        $entity = $this->findOr404Error($id);
        $form = $crud->getForm($entity);

        return $this->handle($request, $entity, $form, Constants::ACTION_UPDATE, $crud->getEditTemplate());
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
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

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
        $view = $this->get('symfonian_id.admin.view.view');
        $view->setParam('page_title', $translator->trans($pageConfigurator->getTitle(), array(), $translationDomain));
        $view->setParam('page_description', $translator->trans($pageConfigurator->getDescription(), array(), $translationDomain));
        $view->setParam('action_method', $translator->trans('page.'.strtolower($action), array(), $translationDomain));
        $view->setParam('use_file_style', $pluginConfigurator->isFileChooserEnabled());
        $view->setParam('use_editor', $pluginConfigurator->isHtmlEditorEnabled());
        $view->setParam('use_numeric', $pluginConfigurator->isNumericEnabled());
        $view->setParam('autocomplete', false);
        $view->setParam('include_javascript', false);
        //Auto complete
        if ($autoComplete->getRouteResource()) {
            $view->setParam('autocomplete', true);
            $view->setParam('ac_config', array(
                'route' => $autoComplete->getRouteResource(),
                'route_callback' => $autoComplete->getRouteCallback(),
                'selector_storage' => $autoComplete->getTargetSelector(),
            ));
        }
        //Date picker
        $view->setParam('use_date_picker', true);
        $view->setParam('date_picker', array(
            'date_format' => $datePicker->getDateFormat(),
            'flatten' => $datePicker->isFlatten(),
        ));
        //External Javascript
        if (!empty($externalJavascript->getIncludFiles())) {
            $view->setParam('include_javascript', true);
            $view->setParam('js_include', array(
                'files' => $externalJavascript->getIncludFiles(),
                'route' => $externalJavascript->getIncludeRoutes(),
            ));
        }

        $driver = $this->container->get('symfonyid.admin.manager.driver_finder')->findDriverForClass(get_class($model));
        $crudFactory = $this->container->get('symfonyid.admin.crud.crud_factory');
        $crudFactory->setDriver($driver);
        $crudFactory->setRequest($request);
        $crudFactory->setTemplate($template);
        $crudFactory->setView($view);

        return $crudFactory->createOrUpdate($form);
    }
}
