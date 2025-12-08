<?php

namespace App\Form;

use App\Dto\WorkspaceDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'Name',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., CMS Workspace',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Describe the purpose of this workspace',
                    'rows'        => 3,
                ],
            ])
            ->add('repository', TextType::class, [
                'label'    => 'Repository URL',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., https://github.com/user/repo.git',
                ],
            ])
            ->add('branch', TextType::class, [
                'label'    => 'Branch',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., main',
                ],
            ])
            ->add('folder', TextType::class, [
                'label'    => 'Folder',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Subfolder name (defaults to workspace key)',
                ],
            ])
            ->add('docRoot', TextType::class, [
                'label'    => 'Document Root',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'public',
                ],
            ])
            ->add('mode', ChoiceType::class, [
                'label'   => 'Mode',
                'choices' => [
                    'Virtual Host' => 'vhost',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subDomains', CollectionType::class, [
                'label'         => 'Subdomains',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'e.g., cms',
                    ],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'prototype'    => true,
                'attr'         => ['class' => 'subdomains-collection'],
            ])
            ->add('disableWeb', CheckboxType::class, [
                'label'    => 'Disable Web Server',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkspaceDto::class,
        ]);
    }
}
