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

use SymfonyId\AdminBundle\Annotation\Page;
use SymfonyId\AdminBundle\Exception\CallMethodBeforeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class PageConfigurator implements ConfiguratorInterface
{
    /**
     * @var Page
     */
    private $page;

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return string
     *
     * @throws CallMethodBeforeException
     */
    public function getTitle()
    {
        if (!$this->page) {
            throw new CallMethodBeforeException('setPage');
        }

        return $this->page->getTitle();
    }

    /**
     * @return string
     *
     * @throws CallMethodBeforeException
     */
    public function getDescription()
    {
        if (!$this->page) {
            throw new CallMethodBeforeException('setPage');
        }

        return $this->page->getDescription();
    }
}
