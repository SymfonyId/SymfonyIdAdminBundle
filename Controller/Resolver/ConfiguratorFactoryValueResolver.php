<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfiguratorFactoryValueResolver extends AbstractValueResolver
{
    /**
     * @var ConfiguratorFactory
     */
    private $configuratorFactory;

    /**
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function __construct(ConfiguratorFactory $configuratorFactory)
    {
        $this->configuratorFactory = $configuratorFactory;
    }

    /**
     * Returns the possible value(s).
     *
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->configuratorFactory;
    }
}
