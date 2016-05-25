<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\ActionHandler;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Crud\CrudOperationHandler;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CreateUpdateActionHandler extends AbstractActionHandler implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var CrudOperationHandler
     */
    private $crudOperationHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param TranslatorInterface  $translator
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, TranslatorInterface $translator)
    {
        $this->crudOperationHandler = $crudOperationHandler;
        $this->translator = $translator;
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
