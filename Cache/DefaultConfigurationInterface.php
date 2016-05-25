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
interface DefaultConfigurationInterface
{
    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function setConfiguration(ConfiguratorFactory $configuratorFactory);
}
