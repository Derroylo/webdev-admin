<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Config\ServicePresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ServiceType extends AbstractType
{
    public function __construct(
        private readonly ServicePresetsServiceInterface $servicePresetsService
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
                'disabled' => true,
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
                'disabled' => true,
                'attr'     => [
                    'class'    => 'form-control',
                    'id'       => 'template-key',
                    'disabled' => 'disabled',
                ],
                'help' => 'Choose a pre-defined service template to auto-fill the form',
            ])
            ->add('key', TextType::class, [
                'label' => 'Name',
                'mapped' => false,
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., mysql',
                    'id'          => 'service-key',
                ],
                'help' => 'The key is used to identify the service in the project configuration',
            ])
            ->add('name', TextType::class, [
                'label' => 'Description',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., MySQL Server - Relational Database',
                    'id'          => 'service-name',
                ],
                'help' => 'A descriptive name for the service (e.g., "MySQL Server - Relational Database")',
            ])
            ->add('category', ChoiceType::class, [
                'label'   => 'Category',
                'choices' => $serviceCategoryChoices,
                'attr'    => [
                    'class' => 'form-control',
                    'id'    => 'service-category',
                ],
                'help' => 'The category helps organize services',
            ])
            ->add('active', CheckboxType::class, [
                'label'    => 'Active',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
                'help' => 'Whether the service should be started automatically',
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
                'help' => 'The port number for the service (1-65535)',
            ])
            ->add('subDomain', TextType::class, [
                'label'    => 'Subdomain',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'e.g., mysql',
                ],
                'help' => 'The subdomain for the service (optional)',
            ])
            ->add('service_definition', TextareaType::class, [
                'label' => 'Service Definition',
                'required' => false,
                'mapped' => false,
                'attr'     => [
                    'class'       => 'form-control',
                    'rows' => 10,
                    'placeholder' => 'redis:
    image: redis:latest
    networks:
      - webdev-network
    container_name: ${COMPOSE_PROJECT_NAME:-devcontainer}-redis
    ports:
      - 6379:6379',
                ],
                'help' => 'The service definition for the docker-compose.yml file',
            ]);
    }
}
