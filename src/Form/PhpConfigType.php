<?php

namespace App\Form;

use App\Dto\PhpConfigDto;
use App\Service\Config\PhpPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhpConfigType extends AbstractType
{
    public function __construct(
        private readonly PhpPresetsServiceInterface $phpPresetsService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build choices from configuration
        $phpVersions = $this->phpPresetsService->getPhpVersions();
        $choices = [];
        foreach ($phpVersions as $versionData) {
            $label = $versionData['label'] ?? 'PHP ' . $versionData['version'];
            $choices[$label] = $versionData['version'];
        }

        $builder
            ->add('version', ChoiceType::class, [
                'label' => 'PHP Version',
                'choices' => $choices,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('opcache_enable', ChoiceType::class, [
                'label' => 'OPcache Enable',
                'choices' => [
                    'On' => 'on',
                    'Off' => 'off',
                ],
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('xdebug_mode', ChoiceType::class, [
                'label' => 'Xdebug Mode',
                'choices' => [
                    'Off' => 'off',
                    'Debug' => 'debug',
                    'Coverage' => 'coverage',
                    'Develop' => 'develop',
                    'Profile' => 'profile',
                ],
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('xdebug_start_with_request', ChoiceType::class, [
                'label' => 'Xdebug Start With Request',
                'choices' => [
                    'Yes' => 'yes',
                    'No' => 'no',
                ],
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('memory_limit_web', TextType::class, [
                'label' => 'Memory Limit (Web)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 512M',
                ],
            ])
            ->add('memory_limit_cli', TextType::class, [
                'label' => 'Memory Limit (CLI)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., -1 for unlimited',
                ],
            ])
            ->add('max_execution_time_cli', TextType::class, [
                'label' => 'Max Execution Time (CLI)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 0 for unlimited',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PhpConfigDto::class,
        ]);
    }
}
