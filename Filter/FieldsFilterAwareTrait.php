<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Filter;

use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait FieldsFilterAwareTrait
{
    /**
     * @var Extractor
     */
    protected $extractorFactory;

    /**
     * @var ConfiguratorFactory
     */
    protected $configuratorFactory;

    /**
     * @var string
     */
    protected $dateTimeFormat;

    /**
     * @var array
     */
    protected $fieldsFilter;

    /**
     * @param Extractor $extractorFactory
     */
    public function setExtractorFactory(Extractor $extractorFactory)
    {
        $this->extractorFactory = $extractorFactory;
    }

    /**
     * @param string $format
     */
    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = $format;
    }

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfigurationFactory(ConfiguratorFactory $configuratorFactory)
    {
        $this->configuratorFactory = $configuratorFactory;
    }

    /**
     * @param array $fieldsFilter
     */
    public function setFieldsFilter(array $fieldsFilter)
    {
        $this->fieldsFilter = $fieldsFilter;
    }
}
