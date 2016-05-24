<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\EventDispatcher\Event;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractController extends BaseController implements ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;

    /**
     * Get controller class name.
     *
     * @return string
     */
    abstract protected function getClassName();

    public function subscribeEvent($eventName, Event $event)
    {
        /** @var \SymfonyId\AdminBundle\Event\EventSubscriber $eventSubscriber */
        $eventSubscriber = $this->container->get('symfonyid.admin.event.event_subscriber');
        $eventSubscriber->subscribe($eventName, $event);
    }
}
