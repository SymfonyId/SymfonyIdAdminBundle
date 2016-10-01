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
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\UtilConfigurator;
use SymfonyId\AdminBundle\Controller\AnnotationConfigurationAwareInterface;
use SymfonyId\AdminBundle\Controller\AnnotationConfigurationAwareTrait;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\Upload\UploadHandler;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UploadFileSubscriber implements ConfigurationAwareInterface, AnnotationConfigurationAwareInterface, EventSubscriberInterface
{
    use AnnotationConfigurationAwareTrait;
    use ConfigurationAwareTrait;

    /**
     * @var UploadHandler
     */
    private $uploadHandler;

    /**
     * @var array
     */
    private $uploadDirectory;

    /**
     * @param UploadHandler $uploadHandler
     * @param array         $uploadDirectory
     */
    public function __construct(UploadHandler $uploadHandler, array $uploadDirectory)
    {
        $this->uploadHandler = $uploadHandler;
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function setController(FilterControllerEvent $event)
    {
        if (!$this->isValidListener($event) || !$event->isMasterRequest()) {
            return;
        }
    }

    /**
     * @param FilterModelEvent $event
     */
    public function upload(FilterModelEvent $event)
    {
        $configurationFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));

        /** @var UtilConfigurator $utilConfigurator */
        $utilConfigurator = $configurationFactory->getConfigurator(UtilConfigurator::class);
        $upload = $utilConfigurator->getUpload();

        if ($upload && $upload->getUploadable()) {
            $this->uploadHandler->setFields(array($upload->getUploadable()), array($upload->getTargetField()));
            $this->uploadHandler->setUploadDir($this->uploadDirectory['server_path']);

            $model = $event->getModel();
            $this->uploadHandler->upload($model);
            $event->setModel($model);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('setController', -127),
            ),
            Constants::PRE_SAVE => array(
                array('upload', 0),
            ),
        );
    }
}
