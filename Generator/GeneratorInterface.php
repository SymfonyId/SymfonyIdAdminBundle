<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface GeneratorInterface
{
    public function getClassName();

    public function getClassPath();

    public function generate(BundleInterface $bundle, $entity, ClassMetadata $classMetadata, $forceOverwrite = false);

    public function getFieldsFromMetadata(ClassMetadata $classMetadata);
}
