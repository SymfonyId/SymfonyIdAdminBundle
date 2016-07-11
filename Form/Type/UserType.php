<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SymfonyId\AdminBundle\Form\DataTransformer\RoleToArrayTransformer;
use SymfonyId\AdminBundle\Form\DataTransformer\StringToFileTransformer;
use SymfonyId\AdminBundle\Form\EventSubscriber\RemovePasswordFieldSubscriber;
use SymfonyId\AdminBundle\Security\RoleHierarchyListBuilder;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @var array
     */
    private $roleHierarchy;

    /**
     * @var array
     */
    private $uploadDir;

    /**
     * @param string $userClass
     * @param string $translationDomain
     * @param array  $roleHierarchy
     * @param array  $uploadDir
     */
    public function __construct($userClass, $translationDomain, array $roleHierarchy, array $uploadDir)
    {
        $this->userClass = $userClass;
        $this->translationDomain = $translationDomain;
        $this->roleHierarchy = array_keys($roleHierarchy);
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, array(
                'label' => 'form.label.fullname',
                'widget_form_group_attr' => array(
                    'class' => 'form-group override-group-form-margin override-group-form-padding col-lg-6 col-sm-12 col-xs-12',
                ),
                'label_attr' => array(
                    'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                ),
                'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
            ))
            ->add('username', TextType::class, array(
                'label' => 'form.label.username',
                'widget_form_group_attr' => array(
                    'class' => 'form-group override-group-form-right-margin override-group-form-right-padding col-lg-6 col-sm-12 col-xs-12',
                ),
                'label_attr' => array(
                    'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                ),
                'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
                'attr' => array(
                    'class' => 'username',
                ),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'form.label.email',
                'widget_form_group_attr' => array(
                    'class' => 'form-group override-group-form-margin override-group-form-padding col-lg-6 col-sm-12 col-xs-12',
                ),
                'label_attr' => array(
                    'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                ),
                'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
            ))
            ->add($builder->create('roles', ChoiceType::class, array(
                'label' => 'form.label.role',
                'choices' => RoleHierarchyListBuilder::buildArrayForChoiceType($this->roleHierarchy),
                'placeholder' => 'message.select_empty',
                'widget_form_group_attr' => array(
                    'class' => 'form-group override-group-form-right-margin override-group-form-right-padding col-lg-6 col-sm-12 col-xs-12',
                ),
                'label_attr' => array(
                    'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                ),
                'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
            ))->addModelTransformer(new RoleToArrayTransformer()))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'message.password_must_match',
                'first_options' => array(
                    'widget_form_group_attr' => array(
                        'class' => 'form-group override-group-form-margin override-group-form-padding col-lg-6 col-sm-12 col-xs-12',
                    ),
                    'label_attr' => array(
                        'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                    ),
                    'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
                    'label' => 'form.label.password',
                ),
                'second_options' => array(
                    'widget_form_group_attr' => array(
                        'class' => 'form-group override-group-form-right-margin override-group-form-right-padding col-lg-6 col-sm-12 col-xs-12',
                    ),
                    'label_attr' => array(
                        'class' => 'col-lg-4 col-sm-4 col-xs-4 control-label',
                    ),
                    'horizontal_input_wrapper_class' => 'col-lg-8 col-sm-8 col-xs-8',
                    'label' => 'form.label.repeat_password',
                ),
                'required' => true,
            ))
            ->add($builder->create('file', FileType::class, array(
                'label' => 'form.label.avatar',
                'required' => false,
                'attr' => array(
                    'accept' => 'image/*',
                    'class' => 'form-control',
                ),
            ))->addModelTransformer(new StringToFileTransformer($this->uploadDir)))
        ;

        $builder->addEventSubscriber(new RemovePasswordFieldSubscriber());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->userClass,
            'translation_domain' => $this->translationDomain,
            'validation_groups' => array('Default'),
            'intention' => 'user',
        ));
    }
}
