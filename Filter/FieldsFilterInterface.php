<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Filter;

use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface FieldsFilterInterface
{
    /**
     * @param ExtractorFactory $extractor
     */
    public function setExtractor(ExtractorFactory $extractor);

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfigurator(ConfiguratorFactory $configuratorFactory);

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat);

    /**
     * @param string $key
     * @param string $param
     */
    public function setParameter($key, $param);
}
