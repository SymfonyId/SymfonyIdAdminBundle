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
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Configuration\PageConfigurator;
use SymfonyId\AdminBundle\Configuration\PluginConfigurator;
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

    private function createOrUpdate(Request $request, ModelInterface $data, FormInterface $form, $action, $template)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);
        /* @var PageConfigurator $pageConfigurator */
        $pageConfigurator = $configuratorFactory->getConfigurator(PageConfigurator::class);
        /** @var PluginConfigurator $pluginConfigurator */
        $pluginConfigurator = $configuratorFactory->getConfigurator(PluginConfigurator::class);
        /** @var AutoComplete $autoComplete */
        $autoComplete = $configuration->getConfiguration(AutoComplete::class);
        /** @var DatePicker $datePicker */
        $datePicker = $configuration->getConfiguration(DatePicker::class);
        /** @var ExternalJavascript $externalJavascript */
        $externalJavascript = $configuration->getConfiguration(ExternalJavascript::class);

        /** @var View $view */
        $view = $this->get('symfonian_id.admin.view.view');
        $view->setParam('page_title', $translator->trans($page->getTitle(), array(), $translationDomain));
        $view->setParam('page_description', $translator->trans($page->getDescription(), array(), $translationDomain));
        $view->setParam('action_method', $translator->trans('page.'.strtolower($action), array(), $translationDomain));
        $view->setParam('use_file_style', $util->isUseFileChooser());
        $view->setParam('use_editor', $util->isUseHtmlEditor());
        $view->setParam('use_numeric', $util->isUseNumeric());
        $view->setParam('autocomplete', false);
        $view->setParam('include_javascript', false);
        //Auto complete
        if ($autoComplete->getRouteStore()) {
            $view->setParam('autocomplete', true);
            $view->setParam('ac_config', array(
                'route' => $autoComplete->getRouteStore(),
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
        if (!empty($externalJavascript->getFiles())) {
            $view->setParam('include_javascript', true);
            $view->setParam('js_include', array(
                'files' => $externalJavascript->getFiles(),
                'route' => $externalJavascript->getRoutes(),
            ));
        }

        $handler = $this->getHandler($configuration->getDriver($crud->getEntityClass()), $crud->getEntityClass());
        $handler->setTemplate($template);
        $handler->createNewOrUpdate($this, $request, $data, $form);

        return $handler->getResponse();
    }
}
