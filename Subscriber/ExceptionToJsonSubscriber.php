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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ExceptionToJsonSubscriber implements EventSubscriberInterface
{
    public function convertException(GetResponseForExceptionEvent $exceptionEvent)
    {
        $request = $exceptionEvent->getRequest();
        if (!$exceptionEvent->isMasterRequest() || !$request->isXmlHttpRequest()) {
            return;
        }

        $exception = $exceptionEvent->getException();
        if ($exception instanceof NotFoundHttpException) {
            return new JsonResponse($exceptionEvent->getException()->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array(
                array('convertException', 255),
            ),
        );
    }
}
