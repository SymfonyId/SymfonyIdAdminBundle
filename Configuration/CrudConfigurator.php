<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormFactory;
use SymfonyId\AdminBundle\Annotation\Crud;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudConfigurator implements ContainerAwareInterface, ConfiguratorInterface
{
    use ContainerAwareTrait;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Crud
     */
    private $crud;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return Crud
     */
    public function getCrud()
    {
        return $this->crud;
    }

    /**
     * @param Crud $crud
     */
    public function setCrud(Crud $crud)
    {
        $this->crud = $crud;
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\Template
     */
    public function getTemplate()
    {
        return $this->crud->getTemplate();
    }

    /**
     * @param ModelInterface|null $formData
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws RuntimeException
     */
    public function getForm(ModelInterface $formData = null)
    {
        if (!$this->crud) {
            throw new RuntimeException(sprintf('You must call "setCrud()" before call this method'));
        }

        $formClass = $this->crud->getForm();

        if (class_exists($formClass)) {
            $formObject = new $formClass();
        } else {
            $formObject = $this->container->get($formClass);
        }

        $form = $this->formFactory->create(get_class($formObject));
        $form->setData($formData);

        return $form;
    }

    /**
     * @return array
     */
    public function getGridAction()
    {
        $action = array();

        if ($this->crud->isAllowEdit()) {
            $action[] = Constants::GRID_ACTION_EDIT;
        }
        if ($this->crud->isAllowShow()) {
            $action[] = Constants::GRID_ACTION_SHOW;
        }
        if ($this->crud->isAllowDelete()) {
            $action[] = Constants::GRID_ACTION_DELETE;
        }

        return $action;
    }
}
