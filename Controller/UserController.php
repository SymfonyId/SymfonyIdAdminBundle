<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use SymfonyId\AdminBundle\Annotation as Siab;
use SymfonyId\AdminBundle\Configuration\ConfiguratorFactory;
use SymfonyId\AdminBundle\Configuration\CrudConfigurator;
use SymfonyId\AdminBundle\Form\Type\AdminChangePasswordType;

/**
 * @Route("/user")
 *
 * @Siab\Page(title="page.user.title", description="page.user.description")
 * @Siab\Plugin(fileChooser=true, inlineForm=true)
 * @Siab\Util(upload=@Siab\Upload(uploadable="file", targetField="avatar"))
 * @Siab\Crud(template=@Siab\Template(list="SymfonyIdAdminBundle:User:list.html.twig"))
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserController extends CrudController
{
    use ChangePasswordTrait;

    /**
     * @Route("/{id}/change-password/")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request, $id)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonyid.admin.translation_domain');

        /** @var \FOS\UserBundle\Model\UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id' => $id));
        $this->isGrantedOr403Error($user);

        /** @var ConfiguratorFactory $configuratorFactory */
        $configuratorFactory = $this->getConfiguratorFactory($this->getClassName());
        /** @var CrudConfigurator $crudConfigurator */
        $crudConfigurator = $configuratorFactory->getConfigurator(CrudConfigurator::class);

        $driver = $this->get('symfonyid.admin.manager.driver_finder')->findDriverForClass($crudConfigurator->getCrud()->getModelClass());

        $form = $this->container->get('form.factory')->create(AdminChangePasswordType::class);
        $form->setData($user);
        $form->handleRequest($request);

        /** @var \SymfonyId\AdminBundle\View\View $view */
        $view = $this->getView($translator, $translationDomain);
        $view->setParam('form', $form->createView());

        $this->handleRequest($request, $user, $driver, $view, $form, $translator, $translationDomain);

        return $this->render($this->container->getParameter('symfonyid.admin.themes.change_password'), $view->getParams());
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return get_class($this);
    }
}
