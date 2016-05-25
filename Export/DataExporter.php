<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Export;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Manager\ManagerFactory;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Util\MethodInvoker;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DataExporter
{
    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var int
     */
    private $maxRecords;

    /**
     * @param ManagerFactory $managerFactory
     * @param int            $maxRecords
     */
    public function __construct(ManagerFactory $managerFactory, $maxRecords)
    {
        $this->managerFactory = $managerFactory;
        $this->maxRecords = $maxRecords;
    }

    /**
     * @param Driver $driver
     * @param array  $columns
     *
     * @return StreamedResponse
     */
    public function exportToExcel(Driver $driver, array $columns)
    {
        $csvData = $this->findAllRecords($driver, $columns);
        $response = new StreamedResponse(function () use ($csvData) {
            $resources = fopen('php://output', 'w');

            foreach ($csvData as $item) {
                fputcsv($resources, $item);
            }
        });

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.csv', date('YmdHis')));

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param Driver $driver
     * @param $requestRecords
     *
     * @return bool
     */
    public function isAllowExport(Driver $driver, $requestRecords)
    {
        $totalResult = $this->managerFactory->getManager($driver)->count();
        if ($requestRecords < $totalResult) {
            return false;
        }

        return true;
    }

    /**
     * @param Driver $driver
     * @param array  $columns
     *
     * @return array
     *
     * @throws \SymfonyId\AdminBundle\Exception\RuntimeException
     */
    private function findAllRecords(Driver $driver, array $columns)
    {
        $columns = array_merge(array('id'), $columns);
        $output = array($columns);

        /** @var ModelInterface $record */
        foreach ($this->managerFactory->getManager($driver)->paginate(0, $this->maxRecords) as $record) {
            $temp = array();
            foreach ($columns as $column) {
                $temp[] = MethodInvoker::invokeGet($record, $column);
            }

            $output[] = $temp;
        }

        return $output;
    }
}
