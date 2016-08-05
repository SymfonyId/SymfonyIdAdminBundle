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
use SymfonyId\AdminBundle\Cache\CacheHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CacheHandlerValueResolver extends AbstractValueResolver
{
    /**
     * @var CacheHandler
     */
    private $cacheHandler;

    /**
     * @param CacheHandler $cacheHandler
     */
    public function __construct(CacheHandler $cacheHandler)
    {
        $this->cacheHandler = $cacheHandler;
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
        yield $this->cacheHandler;
    }
}
