<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;
use SymfonyId\AdminBundle\Exception\PermissionException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CacheHandler
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $file
     * @param string $content
     * @throws PermissionException
     */
    public function writeCache($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            @chmod($file, 0666 & ~umask());

            return;
        }

        throw new PermissionException($file);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return bool
     */
    public function hasCache(\ReflectionClass $reflectionClass)
    {
        if ('prod' !== strtolower($this->kernel->getEnvironment())) {
            return false;
        }

        if (file_exists($this->loadCache($reflectionClass)) && 'prod' === strtolower($this->kernel->getEnvironment())) {
            return true;
        }
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    public function loadCache(\ReflectionClass $reflectionClass)
    {
        return sprintf('%s/%s/%s.php.cache', $this->kernel->getCacheDir(), Constants::CACHE_DIR, str_replace('\\', '_', $reflectionClass->getName()));
    }
}
