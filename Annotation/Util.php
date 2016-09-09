<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("autoComplete", type = "\SymfonyId\AdminBundle\Annotation\AutoComplete"),
 *   @Attribute("datePicker", type = "\SymfonyId\AdminBundle\Annotation\DatePicker"),
 *   @Attribute("externalJavascript", type = "\SymfonyId\AdminBundle\Annotation\ExternalJavascript"),
 *   @Attribute("upload", type = "\SymfonyId\AdminBundle\Annotation\Upload"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Util
{
    /**
     * @var AutoComplete
     */
    private $autoComplete;

    /**
     * @var DatePicker
     */
    private $datePicker;

    /**
     * @var ExternalJavascript
     */
    private $externalJavascript;

    /**
     * @var Upload
     */
    private $upload;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['autoComplete'])) {
            $this->autoComplete = $data['autoComplete'];
        }

        if (isset($data['datePicker'])) {
            $this->datePicker = $data['datePicker'];
        }

        if (isset($data['externalJavascript'])) {
            $this->externalJavascript = $data['externalJavascript'];
        }

        if (isset($data['upload'])) {
            $this->upload = $data['upload'];
        }

        unset($data);
    }

    /**
     * @param AutoComplete $autoComplete
     */
    public function setAutoComplete(AutoComplete $autoComplete)
    {
        $this->autoComplete = $autoComplete;
    }

    /**
     * @param DatePicker $datePicker
     */
    public function setDatePicker(DatePicker $datePicker)
    {
        $this->datePicker = $datePicker;
    }

    /**
     * @param ExternalJavascript $externalJavascript
     */
    public function setExternalJavascript(ExternalJavascript $externalJavascript)
    {
        $this->externalJavascript = $externalJavascript;
    }

    /**
     * @param Upload $upload
     */
    public function setUpload(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * @return AutoComplete
     */
    public function getAutoComplete()
    {
        return $this->autoComplete;
    }

    /**
     * @return DatePicker
     */
    public function getDatePicker()
    {
        return $this->datePicker;
    }

    /**
     * @return ExternalJavascript
     */
    public function getExternalJavascript()
    {
        return $this->externalJavascript;
    }

    /**
     * @return Upload
     */
    public function getUpload()
    {
        return $this->upload;
    }
}
