<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyId\AdminBundle\Annotation\Serialize;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface RestResourceAwareInterface
{
    /**
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, FormInterface $form);

    /**
     * @param FormInterface $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCreateForm(FormInterface $form);

    /**
     * @param Request       $request
     * @param FormInterface $form
     * @param int           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, FormInterface $form, $id);

    /**
     * @param FormInterface $form
     * @param int           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUpdateForm(FormInterface $form, $id);

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remove($id);

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSingle($id);

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCollection(Request $request);

    /**
     * @param Serialize $serialize
     */
    public function setSerialization(Serialize $serialize);
}
