<?php

namespace App\Form;

use App\Dto\ServiceDto;
use App\Service\Config\ServicePresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function __construct(
        private readonly ServicePresetsServiceInterface $servicePresetsService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build category choices from available service template files
        $templateCategories      = $this->servicePresetsService->getServiceCategories();
        $templateCategoryChoices = ['-- Manual Entry --' => ''];
        foreach ($templateCategories as $category) {
            $label                           = ucwords(str_replace('_', ' ', $category));
            $templateCategoryChoices[$label] = $category;
        }

        // Build service category choices for the actual category field
        $serviceCategories      = $this->servicePresetsService->getValidServiceCategories();
        $serviceCategoryChoices = [];
        foreach ($serviceCategories as $category) {
            $serviceCategoryChoices[ucfirst($category)] = $category;
        }

        $builder
            ->add('template_category', ChoiceType::class, [
                'label'    => 'Template Category',
                'choices'  => $templateCategoryChoices,
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                    'id'    => 'template-category',
                ],
                'help' => 'Select a category to choose from pre-defined service templates',
            ])
            ->add('template_key', ChoiceType::class, [
                'label'    => 'Service Template',
                'choices'  => ['-- Select a category first --' => ''],
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class'    => 'form-control',
                    'id'       => 'template-key',
                    'disabled' => 'disabled',
                ],
                'help' => 'Choose a pre-defined service template to auto-fill the form',
            ])
            ->add('name', TextType::class, [
                'label' => 'Service Name',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., MySQL Server - Relational Database',
                    'id'          => 'service-name',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label'   => 'Service Category',
                'choices' => $serviceCategoryChoices,
                'attr'    => [
                    'class' => 'form-control',
                    'id'    => 'service-category',
                ],
            ])
            ->add('active', CheckboxType::class, [
                'label'    => 'Active',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('port', IntegerType::class, [
                'label'    => 'Port',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., 8080',
                    'min'         => 1,
                    'max'         => 65535,
                ],
            ])
            ->add('subDomain', TextType::class, [
                'label'    => 'Subdomain',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., mysql',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceDto::class,
        ]);
    }
}
