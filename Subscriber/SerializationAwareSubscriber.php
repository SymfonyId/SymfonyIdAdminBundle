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
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Annotation\Serialize;
use SymfonyId\AdminBundle\Cache\CacheHandler;
use SymfonyId\AdminBundle\Controller\RestResourceAwareInterface;
use SymfonyId\AdminBundle\Extractor\Extractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SerializationAwareSubscriber implements EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var CacheHandler
     */
    private $cacheHandler;

    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * @param KernelInterface $kernel
     * @param CacheHandler    $cacheHandler
     * @param Extractor       $extractor
     */
    public function __construct(KernelInterface $kernel, CacheHandler $cacheHandler, Extractor $extractor)
    {
        $this->kernel = $kernel;
        $this->cacheHandler = $cacheHandler;
        $this->extractor = $extractor;
    }

    public function setSerialization(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        if (!$controller[0] instanceof RestResourceAwareInterface) {
            return;
        }

        $reflectionClass = new \ReflectionClass(Serialize::class);
        if ($this->isProduction() && $this->cacheHandler->hasCache($reflectionClass)) {
            $controller[0]->setSerialization($this->cacheHandler->loadCache($reflectionClass));
        }

        $reflectionObject = new \ReflectionObject($controller[0]);
        $reflectionMethod = $reflectionObject->getMethod($controller[1]);

        $serialize = new Serialize();
        $annotations = $this->extractor->extract($reflectionMethod, Extractor::METHOD_ANNOTATAION);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Serialize) {
                $serialize = $annotation;

                break;
            }
        }

        $controller[0]->setSerialization($serialize);
        $this->cacheHandler->writeCache($reflectionClass, $serialize);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('setSerialization', 0),
            ),
        );
    }

    /**
     * @return bool
     */
    private function isProduction()
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return true;
        }

        return false;
    }
}
