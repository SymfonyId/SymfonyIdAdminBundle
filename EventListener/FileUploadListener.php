<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareInterface;
use SymfonyId\AdminBundle\Configuration\ConfigurationAwareTrait;
use SymfonyId\AdminBundle\Configuration\UtilConfigurator;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\Upload\UploadHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FileUploadListener implements ConfigurationAwareInterface, CrudControllerListenerAwareInterface
{
    use CrudControllerListenerAwareTrait;
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
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }
    }

    /**
     * @param FilterModelEvent $event
     */
    public function onPreSave(FilterModelEvent $event)
    {
        if ($this->uploadHandler->isUploadable()) {
            $configurationFactory = $this->getConfiguratorFactory(new \ReflectionObject($this->controller));

            /** @var UtilConfigurator $utilConfigurator */
            $utilConfigurator = $configurationFactory->getConfigurator(UtilConfigurator::class);
            $upload = $utilConfigurator->getUpload();

            $this->uploadHandler->setFields(array($upload->getUploadable()), array($upload->getTargetField()));
            $this->uploadHandler->setUploadDir($this->uploadDirectory['server_path']);

            $model = $event->getModel();
            $this->uploadHandler->upload($model);
            $event->setModel($model);
        }
    }
}
