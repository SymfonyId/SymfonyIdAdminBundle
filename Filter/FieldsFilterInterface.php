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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface FieldsFilterInterface
{
    /**
     * @param Extractor $extractor
     */
    public function setExtractorFactory(Extractor $extractor);

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfigurationFactory(ConfiguratorFactory $configuratorFactory);

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat);

    /**
     * @param array $fieldsFilter
     */
    public function setFieldsFilter(array $fieldsFilter);

    /**
     * @param ClassMetadata $metadata
     * @param string        $alias
     */
    public function filter(ClassMetadata $metadata, $alias);

    /**
     * @param string $key
     * @param string $param
     */
    public function setParameter($key, $param);
}
