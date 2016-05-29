<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use SymfonyId\AdminBundle\User\User;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GenerateUserAvatarFunction extends \Twig_Extension implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    private $uploadDir;

    /**
     * @param array $uploadDir
     */
    public function __construct(array $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('generate_avatar', array($this, 'generateAvatar')),
        );
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function generateAvatar(User $user)
    {
        if ($user->getAvatar()) {
            return $this->uploadDir['web_path'].$user->getAvatar();
        } else {
            return $this->container->getParameter('symfonyid.admin.user.default_avatar');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'generate_avatar';
    }
}
