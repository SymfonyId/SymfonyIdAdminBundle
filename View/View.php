<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\View;

use SymfonyId\AdminBundle\Exception\RuntimeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class View
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        throw new RuntimeException(sprintf('Parameter with key "%s" is not found.', $key));
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $param) {
            $this->setParam($key, $param);
        }
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
