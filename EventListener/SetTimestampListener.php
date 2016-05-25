<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyId\AdminBundle\Event\FilterModelEvent;
use SymfonyId\AdminBundle\Model\TimestampAwareInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SetTimestampListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FilterModelEvent $event
     */
    public function onPreSaveUser(FilterModelEvent $event)
    {
        $model = $event->getModel();
        if (!$model instanceof TimestampAwareInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $now = new \DateTime();
        $username = $token->getUsername();

        if (!$model->getId()) {
            $model->setCreatedAt($now);
            $model->setCreatedBy($username);
        }

        $model->setUpdatedAt($now);
        $model->setUpdatedBy($username);
    }
}
