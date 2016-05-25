<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud\ActionHandler;

use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Crud\CrudOperationHandler;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DeleteActionHandler extends AbstractActionHandler
{
    /**
     * @var CrudOperationHandler
     */
    private $crudOperationHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param TranslatorInterface  $translator
     * @param string               $translationDomain
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, TranslatorInterface $translator, $translationDomain)
    {
        $this->crudOperationHandler = $crudOperationHandler;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
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
        $this->view->setParam('errors', false);
        $this->view->setParam('message', 'ok');

        if (!$this->crudOperationHandler->remove($driver, $this->model)) {
            $this->view->setParam('errors', true);
            $this->view->setParam('message', $this->translator->trans($this->crudOperationHandler->getErrorMessage(), array(), $this->translationDomain));
        }

        return $this->view;
    }
}
