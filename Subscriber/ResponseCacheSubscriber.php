<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ResponseCacheSubscriber implements EventSubscriberInterface
{
    private $cacheLifetime;

    public function __construct($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    public function cacheResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->isMethod('GET') && $event->isMasterRequest()) {
            $response = $event->getResponse();

            $response->setPublic();
            $response->setMaxAge($this->cacheLifetime);
            $response->setSharedMaxAge($this->cacheLifetime);
            $response->setEtag(md5($response->getContent()));
        }
    }

    public function validateCache(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isMethod('GET') && $event->isMasterRequest()) {
            $response = new Response();

            if ($response->isNotModified($request)) {
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array('validateCache', 255),
            ),
            KernelEvents::RESPONSE => array(
                array('cacheResponse', -127),
            ),
        );
    }
}
