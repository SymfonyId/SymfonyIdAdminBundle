<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Router;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class FormNormalizer
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function normalize(FormInterface $form)
    {
        if (empty(!$form->all())) {
            $result = array();
            $result[$form->getName()] = array();
            foreach ($form->all() as $name => $child) {
                $result[$form->getName()][$name] = $this->normalize($child);
            }

            return $result;
        }

        $view = $form->createView();
        $flatten = array(
            'name' => $view->vars['full_name'],
            'type' => $view->vars['block_prefixes'][1],
            'required' => array_key_exists('required', $view->vars) ? $view->vars['required'] : false,
            'data' => $form->getData(),
        );

        if (array_key_exists('storage', $view->vars['attr']) && array_key_exists('route', $view->vars['attr']['storage'])) {
            if (array_key_exists('parameters', $view->vars['attr']['storage'])) {
                $flatten['storage'] = $this->router->generate($view->vars['attr']['storage']['route'], $view->vars['attr']['storage']['parameters']);
            } else {
                $flatten['storage'] = $this->router->generate($view->vars['attr']['storage']['route']);
            }
        }

        return $flatten;
    }
}
