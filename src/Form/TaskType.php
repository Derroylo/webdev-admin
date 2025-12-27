<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', TextType::class, [
                'label' => 'Name',
                'mapped' => false,
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., install-composer-dependencies',
                ],
                'help' => 'The key is used to identify the task in the project configuration',
            ])
            ->add('name', TextType::class, [
                'label' => 'Description',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., Install composer dependencies',
                ],
                'help' => 'A descriptive name for the task',
            ])
            ->add('onlyMain', CheckboxType::class, [
                'label'    => 'Only Main (Run only in main workspace)',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
                'help' => 'If checked, this task will only run in the main workspace',
            ])
            ->add('init', CollectionType::class, [
                'label'         => 'Init Commands',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr'  => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'attr'         => ['class' => 'collection-container'],
                'help' => 'Commands to run before the task is created',
            ])
            ->add('create', CollectionType::class, [
                'label'         => 'Create Commands',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr'  => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'attr'         => ['class' => 'collection-container'],
                'help' => 'Commands to run after the task is created',
            ])
            ->add('start', CollectionType::class, [
                'label'         => 'Start Commands',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr'  => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'attr'         => ['class' => 'collection-container'],
                'help' => 'Commands to run after the task is started',
            ]);
    }
}
