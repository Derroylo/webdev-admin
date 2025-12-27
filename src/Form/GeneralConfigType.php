<?php

namespace App\Form;

use App\Dto\Project\AbstractProjectConfigDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workspaceFolder', TextType::class, [
                'label' => 'Workspace Folder',
                'required' => false,
                'property_path' => 'config.workspaceFolder',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'workspaces',
                ],
                'help' => 'Folder where additional workspaces (projects) are located',
            ])
            ->add('proxyDomain', TextType::class, [
                'label' => 'Proxy Domain',
                'required' => false,
                'property_path' => 'config.proxy.domain',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'dev.localhost',
                ],
                'help' => 'Default domain for the development environment (e.g., mailpit.dev.localhost)',
            ])
            ->add('proxySubDomain', TextType::class, [
                'label' => 'Proxy Subdomain',
                'required' => false,
                'property_path' => 'config.proxy.subDomain',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'devcontainer',
                ],
                'help' => 'Default subdomain for the workspace (e.g., shop.dev.localhost)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractProjectConfigDto::class,
        ]);
    }
}
