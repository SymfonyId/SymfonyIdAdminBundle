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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Util\MethodInvoker;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DetailActionHandler implements ViewHandlerInterface, ContainerAwareInterface
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
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ModelInterface
     */
    private $viewData;

    /**
     * @var array
     */
    private $showFields = array();

    /**
     * @var bool
     */
    private $allowDelete = true;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param View                 $view
     * @param Session              $session
     * @param TranslatorInterface  $translator
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, View $view, Session $session, TranslatorInterface $translator)
    {
        $this->crudOperationHandler = $crudOperationHandler;
        $this->view = $view;
        $this->session = $session;
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
     * @param ModelInterface $viewData
     */
    public function setData(ModelInterface $viewData)
    {
        $this->viewData = $viewData;
    }

    /**
     * @param array $showFields
     */
    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @param null|bool $allowDelete
     *
     * @return bool
     */
    public function isDelete($allowDelete = null)
    {
        if (null !== $allowDelete) {
            $this->allowDelete = $allowDelete;
        }

        return $this->allowDelete;
    }

    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver)
    {
        $referer = $this->session->get('referer');
        $refererHeader = $this->request->headers->get('referer');
        if ($refererHeader) {
            $referer = $refererHeader;
            $this->session->set('referer', $refererHeader);
        }

        $output = array();
        foreach ($this->showFields as $key => $property) {
            if ($value = MethodInvoker::invokeGet($this->viewData, $property)) {
                array_push($output, array(
                    'name' => $property,
                    'value' => $value,
                ));
            }
        }

        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        $this->view->setParam('data', $output);
        $this->view->setParam('menu', $this->container->getParameter('symfonyid.admin.menu'));
        $this->view->setParam('action_method', $this->translator->trans('page.show', array(), $translationDomain));
        $this->view->setParam('back', $referer);
        $this->view->setParam('action', $this->allowDelete);
        $this->view->setParam('number', $this->container->getParameter('symfonyid.admin.number'));
        $this->view->setParam('upload_dir', $this->container->getParameter('symfonyid.admin.upload_dir'));

        return $this->view;
    }
}
