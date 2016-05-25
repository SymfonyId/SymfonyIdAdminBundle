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

use SymfonyId\AdminBundle\Annotation\Util;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UtilConfigurator implements ConfiguratorInterface
{
    /**
     * @var Util
     */
    private $util;

    /**
     * @param Util $util
     */
    public function setUtil(Util $util)
    {
        $this->util = $util;
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\AutoComplete
     */
    public function getAutoComplete()
    {
        return $this->util->getAutoComplete();
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\DatePicker
     */
    public function getDatePicker()
    {
        return $this->util->getDatePicker();
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\ExternalJavascript
     */
    public function getExternalJavascript()
    {
        return $this->util->getExternalJavascript();
    }

    /**
     * @return \SymfonyId\AdminBundle\Annotation\Upload
     */
    public function getUpload()
    {
        return $this->util->getUpload();
    }
}
