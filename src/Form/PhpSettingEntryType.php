<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Config\PhpPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $existingSettings = $options['existing_settings'] ?? [];
        
        // Build grouped choices for Select2
        // Separate common settings from others
        $commonChoices = [];
        $groupedChoices = [];
        $predefinedSettingNames = [];
        
        foreach ($phpSettings as $category => $settings) {
            $categoryLabel = ucfirst($category);
            
            foreach ($settings as $setting) {
                $settingName = $setting['name'] ?? '';
                if (!$settingName) {
                    continue;
                }
                
                $predefinedSettingNames[] = $settingName;
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
        
        // Add existing settings that are not in predefined list
        $customChoices = [];
        foreach ($existingSettings as $settingName) {
            if (!empty($settingName) && !in_array($settingName, $predefinedSettingNames, true)) {
                $customChoices[$settingName] = $settingName;
            }
        }
        
        // Sort common choices
        ksort($commonChoices);
        
        // Sort custom choices
        ksort($customChoices);
        
        // Sort categories
        ksort($groupedChoices);
        
        // Sort settings within each category
        foreach ($groupedChoices as $category => &$settings) {
            ksort($settings);
        }
        
        // Merge: Common first, then custom (if any), then grouped by category
        $finalChoices = [];
        if (!empty($commonChoices)) {
            $finalChoices['★ Commonly Used'] = $commonChoices;
        }
        if (!empty($customChoices)) {
            $finalChoices['Custom Settings'] = $customChoices;
        }
        $finalChoices = array_merge($finalChoices, $groupedChoices);

        $builder
            ->add('settingName', ChoiceType::class, [
                'label' => false,
                'choices' => $finalChoices,
                'choice_value' => function ($value) {
                    return $value;
                },
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

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();

            $choices = [
                $data['settingName'] => $data['settingName'],
            ];

            // Remove and re-add the field with updated choices
            $form->remove('settingName');
            $form->add('settingName', ChoiceType::class, [
                'label' => false,
                'choices' => $choices,
                'placeholder' => 'Search and select a PHP setting...',
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control php-setting-select',
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'category'          => null,
            'data_class'       => null,
            'existing_settings' => [],
        ]);
    }
}

