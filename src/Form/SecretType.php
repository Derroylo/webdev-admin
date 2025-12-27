<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SecretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', TextType::class, [
                'label' => 'Key',
                'mapped' => false,
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., auth',
                ],
            ])
            ->add('missingMessage', TextareaType::class, [
                'label'    => 'Missing Message',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'Custom error message when secret is missing',
                    'rows'        => 2,
                ],
                'help' => 'Optional message to show when this secret is missing',
            ])
            ->add('sourceKey', TextType::class, [
                'label' => 'Source Key',
                'property_path' => 'source.key',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., auth',
                ],
                'help' => 'The file name (without extension) to look for',
            ])
            ->add('sourceGroup', TextType::class, [
                'label'    => 'Source Group',
                'required' => false,
                'property_path' => 'source.group',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., composer',
                ],
                'help' => 'Optional subfolder to search in',
            ])
            ->add('targetFile', TextType::class, [
                'label'    => 'Target File',
                'required' => false,
                'property_path' => 'target.file',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., auth.json',
                ],
                'help' => 'Where to put the secret in your project',
            ])
            ->add('targetEnvVar', TextType::class, [
                'label'    => 'Target Environment Variable',
                'required' => false,
                'property_path' => 'target.envVar',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., file',
                ],
                'help' => 'Make secret available as environment variables',
            ])
            ->add('targetExpectedSecrets', CollectionType::class, [
                'label'         => 'Expected Secrets',
                'property_path' => 'target.expectedSecrets',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'e.g., GitLab',
                    ],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'prototype'    => true,
                'help'         => 'List of secrets that should be in the file',
            ])
            ->add('targetExpectedVars', CollectionType::class, [
                'label'         => 'Expected Variables',
                'property_path' => 'target.expectedVars',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'e.g., DOCKER_USERNAME',
                    ],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'prototype'    => true,
                'help'         => 'List of environment variables that should be in the file',
            ]);
    }
}
