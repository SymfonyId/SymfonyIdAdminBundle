<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DefaultConfigurationFactory
{
    /**
     * @var DefaultConfigurationInterface[]
     */
    private $defaultConfigurations = array();

    /**
     * @param DefaultConfigurationInterface $defaultConfiguration
     */
    public function addDefaultConfiguration(DefaultConfigurationInterface $defaultConfiguration)
    {
        $this->defaultConfigurations[get_class($defaultConfiguration)] = $defaultConfiguration;
    }

    public function build(ConfiguratorFactory $configuratorFactory)
    {
        foreach ($this->defaultConfigurations as $defaultConfiguration) {
            $defaultConfiguration->setConfiguration($configuratorFactory);
        }
    }
}
