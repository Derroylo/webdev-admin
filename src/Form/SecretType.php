<?php

namespace App\Form;

use App\Dto\SecretDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., auth',
                ],
                'help' => 'The file name (without extension) to look for',
            ])
            ->add('sourceGroup', TextType::class, [
                'label'    => 'Source Group',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., composer',
                ],
                'help' => 'Optional subfolder to search in',
            ])
            ->add('targetFile', TextType::class, [
                'label'    => 'Target File',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., auth.json',
                ],
                'help' => 'Where to put the secret in your project',
            ])
            ->add('targetEnvVar', TextType::class, [
                'label'    => 'Target Environment Variable',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., file',
                ],
                'help' => 'Make secret available as environment variables',
            ])
            ->add('targetExpectedSecrets', CollectionType::class, [
                'label'         => 'Expected Secrets',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecretDto::class,
        ]);
    }
}
