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
 *   @Attribute("htmlEditor", type = "boolean"),
 *   @Attribute("fileChooser", type = "boolean"),
 *   @Attribute("inlineForm", type = "boolean"),
 *   @Attribute("numeric", type = "boolean"),
 *   @Attribute("bulkInsert", type = "boolean"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Plugin
{
    /**
     * @var bool
     */
    private $htmlEditor = false;

    /**
     * @var bool
     */
    private $fileChooser = false;

    /**
     * @var bool
     */
    private $inlineForm = false;

    /**
     * @var bool
     */
    private $numeric = false;

    /**
     * @var bool
     */
    private $bulkInsert = false;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['htmlEditor'])) {
            $this->htmlEditor = (bool) $data['htmlEditor'];
        }

        if (isset($data['fileChooser'])) {
            $this->fileChooser = (bool) $data['fileChooser'];
        }

        if (isset($data['inlineForm'])) {
            $this->inlineForm = (bool) $data['inlineForm'];
        }

        if (isset($data['numeric'])) {
            $this->numeric = (bool) $data['numeric'];
        }

        if (isset($data['bulkInsert'])) {
            $this->bulkInsert = (bool) $data['bulkInsert'];
        }

        unset($data);
    }

    /**
     * @return bool
     */
    public function isHtmlEditorEnabled()
    {
        return $this->htmlEditor;
    }

    /**
     * @return bool
     */
    public function isFileChooserEnabled()
    {
        return $this->fileChooser;
    }

    /**
     * @return bool
     */
    public function isInlineFormEnabled()
    {
        return $this->inlineForm;
    }

    /**
     * @return bool
     */
    public function isNumericEnabled()
    {
        return $this->numeric;
    }

    /**
     * @return bool
     */
    public function isBulkInsertEnabled()
    {
        return $this->bulkInsert;
    }
}
