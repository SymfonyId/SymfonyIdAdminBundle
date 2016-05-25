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
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Crud\CrudOperationHandler;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class BulkCreateActionHandler extends AbstractActionHandler
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
     * @var CrudConfigurator
     */
    private $crudConfigurator;

    /**
     * @var string
     */
    private $translationDomain;

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
     * @param CrudConfigurator $crudConfigurator
     */
    public function setCrudConfigurator(CrudConfigurator $crudConfigurator)
    {
        $this->crudConfigurator = $crudConfigurator;
    }

    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver)
    {
        $output = array(
            'count' => 0,
            'data' => array(),
        );

        if ($this->request->isMethod('POST')) {
            $output = $this->doBulkCreate($driver);
        }

        if (0 === count($output['data'])) {
            $message = 'message.insert_bulk_failed';
        } else {
            $message = 'message.insert_bulk';
        }

        $this->view->setParam('status', empty($output['data']) ? false : true);
        $this->view->setParam('message', $this->translator->trans($message, array(
            '%count%' => count($output['data']),
            '%deleted%' => $output['count'],
            '%data%' => implode(', ', $output['data']),
        ), $this->translationDomain));

        return $this->view;
    }

    /**
     * @param Driver $driver
     *
     * @return array
     *
     * @throws \SymfonyId\AdminBundle\Exception\RuntimeException
     */
    private function doBulkCreate(Driver $driver)
    {
        $isInserted = array();
        $countData = 0;
        $modelClass = $this->crudConfigurator->getCrud()->getModelClass();
        $formRequests = $this->request->get('form');

        foreach ($formRequests as $formRequest) {
            /** @var ModelInterface $model */
            $model = new $modelClass();
            $form = $this->crudConfigurator->getForm($model);

            $form->submit($formRequest[$form->getName()]);
            if ($form->isValid()) {
                if (true === $this->crudOperationHandler->save($driver, $model)) {
                    $isInserted[] = $model->getId();
                }

                ++$countData;
            }
        }

        return array(
            'count' => $countData,
            'data' => $isInserted,
        );
    }
}
