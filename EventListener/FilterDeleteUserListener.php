<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\EventListener;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Event\FilterModelEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterDeleteUserListener
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface   $translator
     * @param string                $translationDomain
     */
    public function __construct(TokenStorageInterface $tokenStorage, TranslatorInterface $translator, $translationDomain)
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $this->user = $token->getUser();
        }
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param FilterModelEvent $event
     */
    public function onDeleteUser(FilterModelEvent $event)
    {
        $model = $event->getModel();

        if (!$model instanceof UserInterface) {
            return;
        }

        //Can't delete your self
        if ($this->user->getUsername() === $model->getUsername()) {
            $response = new JsonResponse(array(
                'status' => false,
                'message' => $this->translator->trans('message.cant_delete_your_self', array(), $this->translationDomain),
            ));

            $event->setResponse($response);
        }
    }
}
