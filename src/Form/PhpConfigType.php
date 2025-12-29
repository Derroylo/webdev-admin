<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Config\PhpPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PhpConfigType extends AbstractType
{
    public function __construct(
        private readonly PhpPresetsServiceInterface $phpPresetsService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build choices from configuration
        $phpVersions = $this->phpPresetsService->getPhpVersions();
        $choices     = [];
        foreach ($phpVersions as $versionData) {
            $label           = $versionData['label'] ?? 'PHP ' . $versionData['version'];
            $choices[$label] = $versionData['version'];
        }

        $builder
            ->add('version', ChoiceType::class, [
                'label'         => 'PHP Version',
                'property_path' => 'php.version',
                'choices'       => $choices,
                'attr'          => ['class' => 'form-control'],
            ]);

        // Add collection for general config
        $configBuilder = $builder->create('config', CollectionType::class, [
            'label'         => false,
            'entry_type'    => PhpSettingEntryType::class,
            'property_path' => 'php.config',
            'entry_options' => [
                'label' => false,
            ],
            'allow_add'      => true,
            'allow_delete'   => true,
            'required'       => false,
            'mapped'         => false,
            'attr'           => ['class' => 'php-config-collection'],
        ]);
        $builder->add($configBuilder);

        // Add collection for web config
        $configWebBuilder = $builder->create('configWeb', CollectionType::class, [
            'label'         => false,
            'entry_type'    => PhpSettingEntryType::class,
            'property_path' => 'php.configWeb',
            'entry_options' => [
                'label' => false,
            ],
            'allow_add'      => true,
            'allow_delete'   => true,
            'required'       => false,
            'mapped'         => false,
            'attr'           => ['class' => 'php-config-collection'],
        ]);
        $builder->add($configWebBuilder);

        // Add collection for CLI config
        $configCLIBuilder = $builder->create('configCLI', CollectionType::class, [
            'label'         => false,
            'entry_type'    => PhpSettingEntryType::class,
            'property_path' => 'php.configCLI',
            'entry_options' => [
                'label' => false,
            ],
            'allow_add'      => true,
            'allow_delete'   => true,
            'required'       => false,
            'mapped'         => false,
            'attr'           => ['class' => 'php-config-collection'],
        ]);
        $builder->add($configCLIBuilder);

        // Add event listener to map unmapped fields back to DTO
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data === null) {
                return;
            }

            // Map config collection back to DTO
            $configData = $form->get('config')->getData();
            if (is_array($configData)) {
                $dtoConfig = $this->transformFormDataToDto($configData);
                $this->setValueToPath($data, 'php.config', $dtoConfig);
            }

            // Map configWeb collection back to DTO
            $configWebData = $form->get('configWeb')->getData();
            if (is_array($configWebData)) {
                $dtoConfigWeb = $this->transformFormDataToDto($configWebData);
                $this->setValueToPath($data, 'php.configWeb', $dtoConfigWeb);
            }

            // Map configCLI collection back to DTO
            $configCLIData = $form->get('configCLI')->getData();
            if (is_array($configCLIData)) {
                $dtoConfigCLI = $this->transformFormDataToDto($configCLIData);
                $this->setValueToPath($data, 'php.configCLI', $dtoConfigCLI);
            }

            $event->setData($data);
        });
    }

    /**
     * Transform form collection data to DTO array
     */
    private function transformFormDataToDto(array $formData): array
    {
        if (empty($formData) || !is_array($formData)) {
            return [];
        }

        $dtoData = [];
        foreach ($formData as $item) {
            if (!isset($item['settingName']) || !isset($item['value'])) {
                continue;
            }

            $settingName = $item['settingName'];
            $value       = $item['value'] ?? '';

            // Skip if setting name is empty
            if (empty($settingName)) {
                continue;
            }

            // Prevent duplicates - last one wins
            $dtoData[$settingName] = $value;
        }

        return $dtoData;
    }

    /**
     * Set value to object/array using dot notation path
     */
    private function setValueToPath(&$data, string $path, mixed $value): void
    {
        if ($data === null) {
            return;
        }

        $parts = explode('.', $path);
        $current = &$data;

        // Navigate to the parent of the target property
        for ($i = 0; $i < count($parts) - 1; $i++) {
            $part = $parts[$i];
            if (is_object($current)) {
                if (!isset($current->$part)) {
                    return;
                }
                $current = &$current->$part;
            } elseif (is_array($current)) {
                if (!isset($current[$part])) {
                    return;
                }
                $current = &$current[$part];
            } else {
                return;
            }
        }

        // Set the final value
        $finalPart = $parts[count($parts) - 1];
        if (is_object($current)) {
            $current->$finalPart = $value;
        } elseif (is_array($current)) {
            $current[$finalPart] = $value;
        }
    }
}
