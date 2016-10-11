<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Crud;

use Knp\Component\Pager\Pagination\PaginationInterface;
use SymfonyId\AdminBundle\Util\MethodInvoker;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultRecordsHandler implements RecordsHandlerInterface
{
    /**
     * @param PaginationInterface $pagination
     * @param array               $fields
     *
     * @return Records
     */
    public function process(PaginationInterface $pagination, array $fields)
    {
        $data = array();
        $identifier = array();
        /** @var \SymfonyId\AdminBundle\Model\ModelInterface $record */
        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($fields as $k => $property) {
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

        $records = new Records();
        $records->setData($data);
        $records->setIdentifier($identifier);

        return $records;
    }
}
