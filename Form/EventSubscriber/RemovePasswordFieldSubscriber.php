<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use SymfonyId\AdminBundle\User\User;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RemovePasswordFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_SET_DATA => 'onPostSetData');
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSetData(FormEvent $event)
    {
        $formData = $event->getData();
        if (!$formData instanceof User) {
            return;
        }

        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $event->getForm();
        if (!$formData->getId() || 'user' !== $form->getName()) {
            return;
        }

        //Remove password field on edit
        $form->remove('plainPassword');
    }
}
