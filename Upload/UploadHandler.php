<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyId\AdminBundle\Event\EventSubscriber;
use SymfonyId\AdminBundle\Event\FilterUploadEvent;
use SymfonyId\AdminBundle\Exception\KeyNotMatchException;
use SymfonyId\AdminBundle\Exception\RuntimeException;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Util\CamelCaser;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UploadHandler
{
    /**
     * @var string
     */
    private $dirPath;

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $targetFields = array();

    /**
     * @var EventSubscriber
     */
    private $eventSubscriber;

    /**
     * @param EventSubscriber $eventSubscriber
     */
    public function __construct(EventSubscriber $eventSubscriber)
    {
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * @param string $dirPath
     */
    public function setUploadDir($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    /**
     * @param array $fields
     * @param array $targetFields
     *
     * @throws KeyNotMatchException
     */
    public function setFields(array $fields, array $targetFields)
    {
        if (count($fields) !== count($targetFields)) {
            throw new KeyNotMatchException(count($fields), count($targetFields));
        }

        $this->fields = array_values($fields);
        $this->targetFields = array_values($targetFields);
    }

    /**
     * @return bool
     */
    public function isUploadable()
    {
        if (empty($this->fields)) {
            return false;
        }

        return true;
    }

    /**
     * @param ModelInterface $model
     *
     * @throws RuntimeException
     */
    public function upload(ModelInterface $model)
    {
        if (!$this->isUploadable()) {
            return;
        }

        if (!is_dir($this->dirPath)) {
            mkdir($this->dirPath, 0777, true);
        }

        $file = null;
        foreach ($this->fields as $key => $field) {
            $getter = CamelCaser::underScoresToCamelCase('get_'.$field);

            if (method_exists($model, $getter)) {
                /** @var UploadedFile $file */
                $file = call_user_func_array(array($model, $getter), array());
            }

            if ($file instanceof UploadedFile) {
                if ($file->isExecutable()) {
                    throw new RuntimeException(sprintf('message.upload_executable'));
                }

                $filterUpload = new FilterUploadEvent();
                $filterUpload->setFileUploaded($file);

                $this->eventSubscriber->subscribe(Constants::PRE_UPLOAD, $filterUpload);

                $file = $filterUpload->getFileUploaded();
                $fileName = sha1(uniqid('SIAB_', true)).'.'.$file->getClientOriginalExtension();
                $file->move($this->dirPath, $fileName);

                $this->eventSubscriber->subscribe(Constants::POST_UPLOAD, $filterUpload);

                $setter = CamelCaser::underScoresToCamelCase('set_'.$this->targetFields[$key]);
                if (method_exists($model, $setter)) {
                    call_user_func_array(array($model, $setter), array($fileName));
                }
            }
        }
    }
}
