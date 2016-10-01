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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class FormNormalizer
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @param Router                    $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(Router $router, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function normalize(FormInterface $form)
    {
        if (!empty($form->all())) {
            $result = array();
            $result[$form->getName()] = array();
            foreach ($form->all() as $name => $child) {
                $result[$form->getName()][$name] = $this->normalize($child);
            }

            $view = $form->createView();
            $result[$form->getName()]['_token'] = array(
                'name' => sprintf('%s[%s]', $form->getName(), '_token'),
                'type' => 'hidden',
                'required' => true,
                'data' => $view['_token']->vars['value'],
            );

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
