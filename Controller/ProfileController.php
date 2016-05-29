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

use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\Manager\ManagerFactory;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;
use SymfonyId\AdminBundle\Util\MethodInvoker;
use SymfonyId\AdminBundle\View\View;

/**
 * @Route("/profile")
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class ProfileController extends Controller implements ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;

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
            'menu' => $this->container->getParameter('symfonyid.admin.menu'),
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

        /** @var View $view */
        $view = $this->getView($translator, $translationDomain);
        $view->setParam('form', $form->createView());

        $this->handleRequest($request, $driver, $view, $form, $translator, $translationDomain);

        return $this->render($this->container->getParameter('symfonyid.admin.themes.change_password'), $view->getParams());
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return get_class($this);
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function updateUser(FormInterface $form, Request $request)
    {
        /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory */
        $encoderFactory = $this->container->get('security.encoder_factory');

        $user = $this->getUser();
        /** @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($form->get('current_password')->getData(), $user->getSalt());

        if ($password !== $user->getPassword()) {
            /** @var View $view */
            $view = $this->get('symfonyid.admin.view.view');
            $view->setParam('current_password_invalid', true);

            return $this->render('SymfonyIdAdminBundle:Index:change_password.html.twig', $view->getParams());
        }

        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($form->getData());
    }

    /**
     * @param Request             $request
     * @param Driver              $driver
     * @param View                $view
     * @param FormInterface       $form
     * @param TranslatorInterface $translator
     * @param $translationDomain
     */
    private function handleRequest(Request $request, Driver $driver, View $view, FormInterface $form, TranslatorInterface $translator, $translationDomain)
    {
        if ($request->isMethod('POST')) {
            if (!$form->isValid()) {
                $view->setParam('errors', true);
            } elseif ($form->isValid()) {
                $this->updateUser($form, $request);

                /** @var ManagerFactory $managerFactory */
                $managerFactory = $this->container->get('symfonyid.admin.manager.manager_factory');

                $model = $form->getData();
                $managerFactory->setModelClass(get_class($model));

                $event = new FilterModelEvent();
                $event->setManager($managerFactory->getManager($driver));
                $event->setModel($model);

                $eventSubscriber = $this->get('symfonyid.admin.event.event_subscriber');
                $eventSubscriber->subscribe(Constants::POST_SAVE, $event);

                $view->setParam('success', $translator->trans('message.data_saved', array(), $translationDomain));
            }
        }
    }

    /**
     * @param TranslatorInterface $translator
     * @param $translationDomain
     *
     * @return View
     */
    private function getView(TranslatorInterface $translator, $translationDomain)
    {
        /** @var View $view */
        $view = $this->get('symfonyid.admin.view.view');
        $view->setParam('page_title', $translator->trans('page.change_password.title', array(), $translationDomain));
        $view->setParam('page_description', $translator->trans('page.change_password.description', array(), $translationDomain));
        $view->setParam('form_theme', $this->container->getParameter('symfonyid.admin.themes.form_theme'));
        $view->setParam('menu', $this->container->getParameter('symfonyid.admin.menu'));

        return $view;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    private function isGrantedOr403Error(UserInterface $user)
    {
        $authorizationChecker = $this->get('symfonyid.admin.security.authorization_checker');

        return $authorizationChecker->isGrantedOr403Error($user);
    }
}
