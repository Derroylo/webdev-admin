<?php

namespace App\Form;

use App\Dto\TaskDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Task Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Install composer dependencies',
                ],
            ])
            ->add('onlyMain', CheckboxType::class, [
                'label' => 'Only Main (Run only in main workspace)',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('init', CollectionType::class, [
                'label' => 'Init Commands',
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'attr' => ['class' => 'collection-container'],
            ])
            ->add('create', CollectionType::class, [
                'label' => 'Create Commands',
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'attr' => ['class' => 'collection-container'],
            ])
            ->add('start', CollectionType::class, [
                'label' => 'Start Commands',
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'attr' => ['class' => 'collection-container'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskDto::class,
        ]);
    }
}
