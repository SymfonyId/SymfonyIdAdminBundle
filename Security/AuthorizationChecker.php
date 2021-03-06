<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Security;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Model\BulkDeletableInterface;
use SymfonyId\AdminBundle\SymfonyIdAdminConstrants as Constants;
use SymfonyId\AdminBundle\User\User;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AuthorizationChecker
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @param TranslatorInterface $translator
     * @param $translationDomain
     */
    public function __construct(TranslatorInterface $translator, $translationDomain)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param UserInterface $user
     *
     * @return true
     */
    public function isValidUserOr403Error(UserInterface $user)
    {
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException($this->translator->trans('message.access_denied', array(), $this->translationDomain));
        }

        return true;
    }

    /**
     * @param CrudConfigurator $crudConfigurator
     * @param string           $action
     * @param bool             $default
     *
     * @throws NotFoundHttpException
     *
     * @return bool
     */
    public function isGrantedOr404Error(CrudConfigurator $crudConfigurator, $action, $default = true)
    {
        $crud = $crudConfigurator->getCrud();
        $granted = false;

        switch ($action) {
            case Constants::ACTION_CREATE:
                $granted = $crud->isAllowCreate();
                break;
            case Constants::ACTION_UPDATE:
                $granted = $crud->isAllowEdit();
                break;
            case Constants::ACTION_READ:
                $granted = $crud->isAllowShow();
                break;
            case Constants::ACTION_DELETE:
                $granted = $crud->isAllowDelete();
                break;
            case Constants::ACTION_DOWNLOAD:
                $granted = $crud->isAllowDownload();
                break;
        }

        if (!($granted && $default)) {
            throw new NotFoundHttpException($this->translator->trans('message.request_not_found', array(), $this->translationDomain));
        }

        return $granted;
    }

    public function isGrantedBulkDelete(CrudConfigurator $crudConfigurator)
    {
        $crud = $crudConfigurator->getCrud();
        $allowBulkDelete = false;
        $reflectionModel = new \ReflectionClass($crud->getModelClass());

        if ($reflectionModel->implementsInterface(BulkDeletableInterface::class) && $crud->isAllowDelete()) {
            $allowBulkDelete = true;
        }

        return $allowBulkDelete;
    }
}
