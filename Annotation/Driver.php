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

use SymfonyId\AdminBundle\Exception\DriverNotFoundException;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("value", type = "string"),
 * })
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class Driver
{
    const ORM = 'orm';
    const ODM = 'odm';
    const BOTH = 'both';

    /**
     * @var string
     */
    private $value;

    /**
     * @param array $data
     *
     * @throws DriverNotFoundException when driver not found
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            if (!in_array($data['value'], array(self::ORM, self::ODM))) {
                throw new DriverNotFoundException($data['value']);
            }

            $this->value = $data['value'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->value;
    }
}
