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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DeleteActionHandler implements ViewHandlerInterface, ContainerAwareInterface
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
     * @var ModelInterface
     */
    private $model;

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
    }

    /**
     * @param ModelInterface $data
     */
    public function setData(ModelInterface $data)
    {
        $this->model = $data;
    }

    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver)
    {
        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        $this->view->setParam('errors', false);
        $this->view->setParam('message', 'ok');

        if (!$this->crudOperationHandler->remove($driver, $this->model)) {
            $this->view->setParam('errors', true);
            $this->view->setParam('message', $this->translator->trans($this->crudOperationHandler->getErrorMessage(), array(), $translationDomain));
        }

        return $this->view;
    }
}
