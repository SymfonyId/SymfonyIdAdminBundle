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

use SymfonyId\AdminBundle\Annotation\Plugin;
use SymfonyId\AdminBundle\Exception\CallMethodBeforeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class PluginConfigurator implements ConfiguratorInterface
{
    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * @param Plugin $plugin
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return bool
     *
     * @throws CallMethodBeforeException
     */
    public function isHtmlEditorEnabled()
    {
        if (!$this->plugin) {
            throw new CallMethodBeforeException('setPlugin');
        }

        return $this->plugin->isHtmlEditorEnabled();
    }

    /**
     * @return bool
     *
     * @throws CallMethodBeforeException
     */
    public function isFileChooserEnabled()
    {
        if (!$this->plugin) {
            throw new CallMethodBeforeException('setPlugin');
        }

        return $this->plugin->isFileChooserEnabled();
    }

    /**
     * @return bool
     *
     * @throws CallMethodBeforeException
     */
    public function isNumericEnabled()
    {
        if (!$this->plugin) {
            throw new CallMethodBeforeException('setPlugin');
        }

        return $this->plugin->isNumericEnabled();
    }

    /**
     * @return bool
     *
     * @throws CallMethodBeforeException
     */
    public function isBulkInsertEnabled()
    {
        if (!$this->plugin) {
            throw new CallMethodBeforeException('setPlugin');
        }

        return $this->plugin->isBulkInsertEnabled();
    }
}
