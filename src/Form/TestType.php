<?php

namespace App\Form;

use App\Dto\TestDto;
use App\Service\Config\TestPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function __construct(
        private readonly TestPresetsServiceInterface $testPresetsService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build category choices from available test template files
        $categories      = $this->testPresetsService->getTestCategories();
        $categoryChoices = ['-- Manual Entry --' => ''];
        foreach ($categories as $category) {
            $label                   = ucwords(str_replace('_', ' ', $category));
            $categoryChoices[$label] = $category;
        }

        $builder
            ->add('template_category', ChoiceType::class, [
                'label'    => 'Template Category',
                'choices'  => $categoryChoices,
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                    'id'    => 'template-category',
                ],
                'help' => 'Select a category to choose from pre-defined test templates',
            ])
            ->add('template_key', ChoiceType::class, [
                'label'    => 'Test Template',
                'choices'  => ['-- Select a category first --' => ''],
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class'    => 'form-control',
                    'id'       => 'template-key',
                    'disabled' => 'disabled',
                ],
                'help' => 'Choose a pre-defined test template to auto-fill the form',
            ])
            ->add('name', TextType::class, [
                'label' => 'Test Name',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., Run PHPUnit tests',
                    'id'          => 'test-name',
                ],
            ])
            ->add('commands', CollectionType::class, [
                'label'         => 'Commands',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr'  => ['class' => 'form-control', 'placeholder' => 'Enter command'],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => true,
                'attr'         => ['class' => 'collection-container'],
            ])
            ->add('tests', CollectionType::class, [
                'label'         => 'Dependent Tests',
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr'  => ['class' => 'form-control', 'placeholder' => 'Enter test name'],
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false,
                'attr'         => ['class' => 'collection-container'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TestDto::class,
        ]);
    }
}
