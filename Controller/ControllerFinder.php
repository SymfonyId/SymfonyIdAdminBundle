<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\SplFileInfo;
use SymfonyId\AdminBundle\Exception\RuntimeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerFinder
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
     * Ex: "@AppBundle/Controller"
     *
     * @param string $resource
     *
     * @return array
     */
    public function getAllControllerFromResource($resource)
    {
        $controllerDir = $this->kernel->locateResource($resource);

        $finder = new Finder();
        $finder->name('*Controller.php')->in($controllerDir);

        $controllers = array();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $controllers[] = $this->getReflectionClassFromFile($file);
        }
        $controllers[] = new \ReflectionClass(UserController::class);//Include UserController

        return $controllers;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return \ReflectionClass
     *
     * @throws RuntimeException
     */
    private function getReflectionClassFromFile(SplFileInfo $file)
    {
        $namespace = null;
        if (preg_match('#^namespace\s+(.+?);$#sm', $file->getContents(), $matches)) {
            $namespace = $matches[1];
        }

        $controller = null;
        if ($namespace) {
            $name = substr($file->getRelativePathname(), 0, -4);
            $controller = sprintf('%s\\%s', $namespace, $name);
        }

        if ($controller) {
            return new \ReflectionClass($controller);
        }

        throw new RuntimeException(sprintf('File "%s" is not contain any class.', $file->getFilename()));
    }
}
