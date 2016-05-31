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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Crud\CrudOperationHandler;
use SymfonyId\AdminBundle\Model\BulkDeletableInterface;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class BulkDeleteActionHandler extends AbstractActionHandler
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
     * @var string
     */
    private $modelClass;

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
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param Driver $driver
     *
     * @return View
     */
    public function getView(Driver $driver)
    {
        $output = $this->doBulkDelete($driver, $this->modelClass);

        if (0 === count($output['data'])) {
            $message = 'message.delete_bulk_failed';
        } else {
            $message = 'message.delete_bulk';
        }

        return new JsonResponse(array(
            'status' => empty($output['data']) ? false : true,
            'message' => $this->translator->trans($message, array(
                '%count%' => count($output['data']),
                '%deleted%' => $output['count'],
                '%data%' => implode(', ', $output['data']),
            ), $this->translationDomain),
        ));
    }

    /**
     * @param Driver $driver
     *
     * @return array
     */
    private function doBulkDelete(Driver $driver)
    {
        $isDeleted = array();
        $countData = 0;
        foreach ($this->request->get('id', array()) as $id) {
            $model = $this->crudOperationHandler->find($driver, $id);
            if (!$model instanceof BulkDeletableInterface) {
                return;
            }
            $deleteMessage = $model->getDeleteInformation();

            if (true === $this->crudOperationHandler->remove($driver, $model)) {
                $isDeleted[] = $deleteMessage;
            }

            ++$countData;
        }

        return array(
            'count' => $countData,
            'data' => $isDeleted,
        );
    }
}
