<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CreateUpdateActionHandler implements ViewHandlerInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var CrudOperationHandler
     */
    private $crudOperationHandler;

    /**
     * @var View
     */
    private $view;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param View                 $view
     * @param TranslatorInterface  $translator
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, View $view, TranslatorInterface $translator)
    {
        $this->crudOperationHandler = $crudOperationHandler;
        $this->view = $view;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @param Driver $driver
     *
     * @return View
     *
     * @throws RuntimeException
     */
    public function getView(Driver $driver)
    {
        if (!$this->request) {
            throw new RuntimeException('Call "setRequest()" before call this method.');
        }

        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        $this->form->handleRequest($this->request);

        $this->view->setParam('form', $this->form->createView());
        $this->view->setParam('form_theme', $this->container->getParameter('symfonyid.admin.themes.form_theme'));
        $this->view->setParam('menu', $this->container->getParameter('symfonyid.admin.menu'));

        $this->view->setParam('errors', false);
        if ($this->request->isMethod('POST')) {
            if (!$this->form->isValid()) {
                $this->view->setParam('errors', true);
                $this->view->setParam('message', $this->translator->trans('message.form_not_valid', array(), $translationDomain));
            } else {
                if ($this->crudOperationHandler->save($driver, $this->form->getData())) {
                    $this->view->setParam('success', $this->translator->trans('message.data_saved', array(), $translationDomain));
                } else {
                    $this->view->setParam('errors', true);
                    $this->view->setParam('message', $this->translator->trans($this->crudOperationHandler->getErrorMessage(), array(), $translationDomain));
                }
            }
        }

        return $this->view;
    }
}
