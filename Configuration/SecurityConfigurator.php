<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Configuration;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use SymfonyId\AdminBundle\Annotation\Security;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SecurityConfigurator implements ConfiguratorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Security $security
     */
    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public function isGranted($action)
    {
        switch ($action) {
            case Constants::ACTION_CREATE:
                $granted = $this->authorizationChecker->isGranted($this->security->getCreate());
                break;
            case Constants::ACTION_UPDATE:
                $granted = $this->authorizationChecker->isGranted($this->security->getEdit());
                break;
            case Constants::ACTION_READ:
                $granted = $this->authorizationChecker->isGranted($this->security->getRead());
                break;
            case Constants::ACTION_DELETE:
                $granted = $this->authorizationChecker->isGranted($this->security->getDelete());
                break;
            case Constants::ACTION_DOWNLOAD:
                $granted = $this->authorizationChecker->isGranted($this->security->getDownload());
                break;
            default:
                $granted = false;
                break;
        }

        return $granted;
    }

    /**
     * @return Security
     */
    public function getSecurity()
    {
        return $this->security;
    }
}
