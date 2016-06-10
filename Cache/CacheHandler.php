<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Cache;

use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Exception\PermissionException;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

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
     * @param \ReflectionClass $reflectionClass
     * @param string           $content
     *
     * @throws PermissionException
     */
    public function writeCache(\ReflectionClass $reflectionClass, $content)
    {
        $cacheFile = $this->getCacheFile($reflectionClass);

        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);

            @chmod($cacheDir, 0777 & ~umask());
        }

        $tmpFile = tempnam(dirname($cacheFile), basename($cacheFile));
        if (false !== @file_put_contents($tmpFile, sprintf('<?php return unserialize(\'%s\');', serialize($content))) && @rename($tmpFile, $cacheFile)) {
            @chmod($cacheFile, 0666 & ~umask());

            return;
        }

        throw new PermissionException($cacheFile);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
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

        return false;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    public function loadCache(\ReflectionClass $reflectionClass)
    {
        return $this->getCacheFile($reflectionClass);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    private function getCacheFile(\ReflectionClass $reflectionClass)
    {
        return sprintf('%s/%s/%s.php.cache', $this->kernel->getCacheDir(), Constants::CACHE_DIR, str_replace('\\', '_', $reflectionClass->getName()));
    }
}
