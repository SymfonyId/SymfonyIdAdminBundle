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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Util\MethodInvoker;

/**
 * @Route("/profile")
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class ProfileController extends Controller implements ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;
    use ChangePasswordTrait;

    /**
     * @Route("/")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        $model = $this->getUser();
        $data = array();

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        foreach ($crudConfigurator->getCrud()->getShowFields() as $key => $property) {
            if ($value = MethodInvoker::invokeGet($model, $property)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => $value,
                ));
            }
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        return $this->render($this->container->getParameter('symfonyid.admin.themes.profile'), array(
            'data' => $data,
            'menu' => $this->container->getParameter('symfonyid.admin.menu.menu_name'),
            'page_title' => $translator->trans('page.profile.title', array(), $translationDomain),
            'page_description' => $translator->trans('page.profile.description', array(), $translationDomain),
        ));
    }

    /**
     * @Route("/change_password/")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        $user = $this->getUser();
        $this->isGrantedOr403Error($user);

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());

        $form = $crudConfigurator->getForm($this->getUser());
        $form->handleRequest($request);

        /** @var \SymfonyId\AdminBundle\View\View $view */
        $view = $this->getView($translator, $translationDomain);
        $view->setParam('form', $form->createView());

        $this->handleRequest($request, $user, $driver, $view, $form, $translator, $translationDomain);

        return $this->render($this->container->getParameter('symfonyid.admin.themes.change_password'), $view->getParams());
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return get_class($this);
    }
}
