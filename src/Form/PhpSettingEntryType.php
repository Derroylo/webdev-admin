<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Config\PhpPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhpSettingEntryType extends AbstractType
{
    public function __construct(
        private readonly PhpPresetsServiceInterface $phpPresetsService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $phpSettings = $this->phpPresetsService->getPhpSettings();
        
        // Build grouped choices for Select2
        // Separate common settings from others
        $commonChoices = [];
        $groupedChoices = [];
        
        foreach ($phpSettings as $category => $settings) {
            $categoryLabel = ucfirst($category);
            
            foreach ($settings as $setting) {
                $settingName = $setting['name'] ?? '';
                if (!$settingName) {
                    continue;
                }
                
                $isCommon = $setting['common'] ?? false;
                $label = $settingName;
                
                if ($isCommon) {
                    $commonChoices[$label] = $settingName;
                } else {
                    if (!isset($groupedChoices[$categoryLabel])) {
                        $groupedChoices[$categoryLabel] = [];
                    }
                    $groupedChoices[$categoryLabel][$label] = $settingName;
                }
            }
        }
        
        // Sort common choices
        ksort($commonChoices);
        
        // Sort categories
        ksort($groupedChoices);
        
        // Sort settings within each category
        foreach ($groupedChoices as $category => &$settings) {
            ksort($settings);
        }
        
        // Merge: Common first, then grouped by category
        $finalChoices = [];
        if (!empty($commonChoices)) {
            $finalChoices['★ Commonly Used'] = $commonChoices;
        }
        $finalChoices = array_merge($finalChoices, $groupedChoices);

        $builder
            ->add('settingName', ChoiceType::class, [
                'label' => false,
                'choices' => $finalChoices,
                'placeholder' => 'Search and select a PHP setting...',
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control php-setting-select',
                ],
            ])
            ->add('value', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control php-setting-value',
                    'placeholder' => 'Enter value',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'category' => null,
            'data_class' => null,
        ]);
    }
}

