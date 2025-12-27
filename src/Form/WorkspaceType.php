<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', TextType::class, [
                'label' => 'Key',
                'mapped' => false,
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., cms',
                ],
                'help' => 'The key is used to identify the workspace in the project configuration',
            ])
            ->add('name', TextType::class, [
                'label'    => 'Name',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., CMS Workspace',
                ],
                'help' => 'A descriptive name for the workspace',
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Describe the purpose of this workspace',
                    'rows'        => 3,
                ],
                'help' => 'Describe the purpose of this workspace',
            ])
            ->add('repository', TextType::class, [
                'label'    => 'Repository URL',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., https://github.com/user/repo.git',
                ],
                'help' => 'Git repository URL (will be cloned if provided)',
            ])
            ->add('branch', TextType::class, [
                'label'    => 'Branch',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., main',
                ],
                'help' => 'Branch to clone (defaults to main)',
            ])
            ->add('folder', TextType::class, [
                'label'    => 'Folder',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Subfolder name (defaults to workspace key)',
                ],
                'help' => 'Subfolder name (defaults to workspace key)',
            ])
            ->add('docRoot', TextType::class, [
                'label'    => 'Document Root',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'public',
                ],
                'help' => 'Document root directory (defaults to public)',
            ])
            ->add('mode', ChoiceType::class, [
                'label'   => 'Mode',
                'choices' => [
                    'Virtual Host' => 'vhost',
                ],
                'attr' => ['class' => 'form-control'],
                'help' => 'Workspace mode (currently only vhost is supported)',
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
                'help' => 'Subdomains to access the workspace (optional)',
            ])
            ->add('disableWeb', CheckboxType::class, [
                'label'    => 'Disable Web Server',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
                'help' => 'Disable web server (for non-Apache apps)',
            ]);
    }
}
