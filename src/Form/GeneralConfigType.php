<?php

namespace App\Form;

use App\Dto\GeneralConfigDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'workspaces',
                ],
                'help' => 'Folder where additional workspaces (projects) are located',
            ])
            ->add('proxyDomain', TextType::class, [
                'label' => 'Proxy Domain',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'dev.localhost',
                ],
                'help' => 'Default domain for the development environment (e.g., mailpit.dev.localhost)',
            ])
            ->add('proxySubDomain', TextType::class, [
                'label' => 'Proxy Subdomain',
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
            'data_class' => GeneralConfigDto::class,
        ]);
    }
}
