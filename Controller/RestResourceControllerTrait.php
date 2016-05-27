<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait RestResourceControllerTrait
{
    /**
     * @param $serviceId
     *
     * @return object
     */
    abstract protected function get($serviceId);

    /**
     * @param $parameter
     *
     * @return object
     */
    abstract protected function getParameter($parameter);

    /**
     * @param View $view
     *
     * @return Response
     */
    abstract protected function handleView(View $view);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {

    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getRequestParam(Request $request)
    {
        $params = array(
            'page' => $request->query->get('page', 1),
            'limit' => $request->query->get('limit', $this->getParameter('sir.limit')),
        );

        if ($filter = $request->query->get('q')) {
            $params['q'] = $filter;
        }

        if ($sortBy = $request->query->get('sort_by')) {
            $params['sort_by'] = $sortBy;
        }

        return $params;
    }
}
