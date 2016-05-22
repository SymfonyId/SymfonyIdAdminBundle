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

use SymfonyId\AdminBundle\Configuration\ConfigurationFactory;
use SymfonyId\AdminBundle\Extractor\ExtractorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait FilterableTrait
{
    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * @param ExtractorFactory $extractorFactory
     */
    public function setExtractor(ExtractorFactory $extractorFactory)
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
     * @param ConfigurationFactory $configurationFactory
     */
    public function setConfigurator(ConfigurationFactory $configurationFactory)
    {
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * @return ExtractorFactory
     */
    protected function getExtractor()
    {
        return $this->extractorFactory;
    }

    /**
     * @return ConfigurationFactory
     */
    protected function getConfigurator()
    {
        return $this->configurationFactory;
    }

    /**
     * @return string
     */
    protected function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }
}
