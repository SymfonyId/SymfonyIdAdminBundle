<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterUploadEvent extends Event
{
    /**
     * @var UploadedFile
     */
    private $fileUploaded;

    /**
     * @return UploadedFile
     */
    public function getFileUploaded()
    {
        return $this->fileUploaded;
    }

    /**
     * @param UploadedFile $fileUploaded
     */
    public function setFileUploaded(UploadedFile $fileUploaded)
    {
        $this->fileUploaded = $fileUploaded;
    }
}
