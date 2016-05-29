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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Crud\CrudOperationHandler;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\Export\DataExporter;
use SymfonyId\AdminBundle\Util\MethodInvoker;
use SymfonyId\AdminBundle\View\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ListActionHandler extends AbstractActionHandler implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var CrudOperationHandler
     */
    private $crudOperationHandler;

    /**
     * @var DataExporter
     */
    private $dataExporter;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $gridFields = array();

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var bool
     */
    private $allowCreate = true;

    /**
     * @var bool
     */
    private $allowBulkDelete = true;

    /**
     * @var bool
     */
    private $formatNumber = true;

    /**
     * @param CrudOperationHandler $crudOperationHandler
     * @param DataExporter         $dataExporter
     * @param TranslatorInterface  $translator
     */
    public function __construct(CrudOperationHandler $crudOperationHandler, DataExporter $dataExporter, TranslatorInterface $translator)
    {
        $this->crudOperationHandler = $crudOperationHandler;
        $this->dataExporter = $dataExporter;
        $this->translator = $translator;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param array $gridFields
     */
    public function setGridFields(array $gridFields)
    {
        $this->gridFields = $gridFields;
    }

    /**
     * @param array $actionList
     */
    public function setActionList(array $actionList)
    {
        $this->view->setParam('action', $actionList);
    }

    /**
     * @param null|bool $allow
     *
     * @return bool
     */
    public function isAllowCrate($allow = null)
    {
        if (null !== $allow) {
            $this->allowCreate = (bool) $allow;
        }

        return $this->allowCreate;
    }

    /**
     * @param null|bool $allow
     *
     * @return bool
     */
    public function isAllowBulkDelete($allow = null)
    {
        if (null !== $allow) {
            $this->allowBulkDelete = (bool) $allow;
        }

        return $this->allowBulkDelete;
    }

    /**
     * @param null|bool $format
     *
     * @return bool
     */
    public function isFormatNumber($format = null)
    {
        if (null !== $format) {
            $this->formatNumber = (bool) $format;
        }

        return $this->formatNumber;
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
        $this->view->setParam('menu', $this->container->getParameter('symfonyid.admin.menu'));
        $this->view->setParam('action_method', $this->translator->trans('page.list', array(), $this->container->getParameter('symfonyid.admin.translation_domain')));
        $this->view->setParam('allow_create', $this->allowCreate);
        $this->view->setParam('allow_delete', $this->allowBulkDelete);
        $this->view->setParam('allow_download', $this->dataExporter->isAllowExport($driver, $this->container->getParameter('symfonyid.admin.max_records')));
        $this->view->setParam('number', $this->container->getParameter('symfonyid.admin.number'));
        $this->view->setParam('formating_number', $this->formatNumber);

        $this->setHeader();
        $this->setRecords($driver);

        return $this->view;
    }

    /**
     * @return array
     */
    private function setHeader()
    {
        $translator = $this->translator;
        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        $header = array_map(function ($value) use ($translator, $translationDomain) {
            return array(
                'title' => $translator->trans(sprintf('entity.fields.%s', $value), array(), $translationDomain),
                'field' => $value,
                'sortable' => $value === 'action' ? false : true,
            );
        }, array_merge($this->gridFields, array('action')));

        $this->view->setParam('header', $header);
    }

    /**
     * @param Driver $driver
     */
    private function setRecords(Driver $driver)
    {
        $page = $this->request->query->get('page', 1);
        $perPage = $this->container->getParameter('symfonyid.admin.per_page');
        $this->view->setParam('start', ($page - 1) * $perPage);

        $pagination = $this->crudOperationHandler->paginateResult($driver, $this->modelClass, $page, $perPage);
        $this->view->setParam('pagination', $pagination);

        $data = array();
        $identifier = array();
        /** @var \SymfonyId\AdminBundle\Model\ModelInterface $record */
        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($this->gridFields as $k => $property) {
                $field = $property;
                $numberFormat = array();
                if (is_array($property)) {
                    $field = $property['field'];
                    $numberFormat = $property['format'];
                }

                $result = MethodInvoker::invokeGet($record, $field);
                if (null !== $result) {
                    if (!empty($numberFormat)) {
                        $result = number_format($result, $numberFormat['decimal'], $numberFormat['decimal_point'], $numberFormat['thousand_separator']);
                    }
                } else {
                    $result = '';
                }

                array_push($temp, $result);
            }

            $data[$key] = $temp;
        }

        $this->view->setParam('identifier', $identifier);
        $this->view->setParam('record', $data);
    }
}
