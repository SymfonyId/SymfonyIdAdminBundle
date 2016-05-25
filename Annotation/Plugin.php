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
 *   @Attribute("numeric", type = "boolean"),
 *   @Attribute("bulkInsert", type = "boolean"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Plugin
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
            $this->htmlEditor = (boolean) $data['htmlEditor'];
        }

        if (isset($data['fileChooser'])) {
            $this->fileChooser = (boolean) $data['fileChooser'];
        }

        if (isset($data['numeric'])) {
            $this->numeric = (boolean) $data['numeric'];
        }

        if (isset($data['bulkInsert'])) {
            $this->bulkInsert = (boolean) $data['bulkInsert'];
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
