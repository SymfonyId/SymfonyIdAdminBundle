<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\User\User;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AutoEnableUserListener
{
    private $autoEnable;

    /**
     * @param bool $autoEnable
     */
    public function __construct($autoEnable = false)
    {
        $this->autoEnable = $autoEnable;
    }

    /**
     * @param FilterModelEvent $event
     */
    public function onPreSaveUser(FilterModelEvent $event)
    {
        if (!$this->autoEnable) {
            return;
        }

        $entity = $event->getModel();
        if (!$entity instanceof User) {
            return;
        }

        if ($entity->getId() || $entity->isEnabled()) {
            return;
        }

        $entity->setEnabled(true);
    }
}
