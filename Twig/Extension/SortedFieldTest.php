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

use Symfony\Component\HttpFoundation\Session\Session;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SortedFieldTest extends \Twig_Extension
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('sorted', array($this, 'isSorted')),
        );
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function isSorted($field)
    {
        $sessionField = $this->session->get(Constants::SESSION_SORTED_ID);
        if ($field === $sessionField) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'is_sorted';
    }
}
