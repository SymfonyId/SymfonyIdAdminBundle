<?php

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;
use SymfonyId\AdminBundle\View\View;

trait ChangePasswordTrait
{
    /**
     * @param FormInterface $form
     * @param UserInterface $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function updateUser(FormInterface $form, UserInterface $user)
    {
        /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory */
        $encoderFactory = $this->container->get('security.encoder_factory');

        /** @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $encoderFactory->getEncoder($user);

        if ($form->has('current_password')) {
            if (!$encoder->isPasswordValid($user->getPassword(), $form->get('current_password')->getData(), $user->getSalt())) {
                /** @var View $view */
                $view = $this->container->get('symfonyid.admin.view.view');
                $view->setParam('current_password_invalid', true);
                $view->setParam('form', $form->createView());

                return $this->container->get('templating')->renderResponse('SymfonyIdAdminBundle:Index:change_password.html.twig', $view->getParams());
            }
        }

        /** @var \FOS\UserBundle\Model\UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($form->getData());
    }

    /**
     * @param Request             $request
     * @param UserInterface       $user
     * @param Driver              $driver
     * @param View                $view
     * @param FormInterface       $form
     * @param TranslatorInterface $translator
     * @param string              $translationDomain
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest(Request $request, UserInterface $user, Driver $driver, View $view, FormInterface $form, TranslatorInterface $translator, $translationDomain)
    {
        if ($request->isMethod('POST')) {
            if (!$form->isValid()) {
                $view->setParam('errors', true);
            } elseif ($form->isValid()) {
                if ($response = $this->updateUser($form, $user)) {
                    return $response;
                }

                /** @var \SymfonyId\AdminBundle\Manager\ManagerFactory $managerFactory */
                $managerFactory = $this->container->get('symfonyid.admin.manager.manager_factory');

                $model = $form->getData();
                $managerFactory->setModelClass(get_class($model));

                $event = new FilterModelEvent();
                $event->setManager($managerFactory->getManager($driver));
                $event->setModel($model);

                $eventSubscriber = $this->container->get('symfonyid.admin.event.event_subscriber');
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
    protected function getView(TranslatorInterface $translator, $translationDomain)
    {
        /** @var View $view */
        $view = $this->container->get('symfonyid.admin.view.view');
        $view->setParam('page_title', $translator->trans('page.change_password.title', array(), $translationDomain));
        $view->setParam('page_description', $translator->trans('page.change_password.description', array(), $translationDomain));
        $view->setParam('menu', $this->container->getParameter('symfonyid.admin.menu.menu_name'));

        return $view;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isGrantedOr403Error(UserInterface $user)
    {
        /** @var \SymfonyId\AdminBundle\Security\AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('symfonyid.admin.security.authorization_checker');

        return $authorizationChecker->isValidUserOr403Error($user);
    }
}
