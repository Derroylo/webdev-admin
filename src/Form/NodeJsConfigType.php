<?php

namespace App\Form;

use App\Dto\NodeJsConfigDto;
use App\Service\Config\NodeJsPresetsServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeJsConfigType extends AbstractType
{
    public function __construct(
        private readonly NodeJsPresetsServiceInterface $nodeJsPresetsService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build choices from configuration
        $nodeVersions = $this->nodeJsPresetsService->getNodeJsVersions();
        $choices = [];
        foreach ($nodeVersions as $versionData) {
            $label = $versionData['label'] ?? 'Node.js ' . $versionData['version'];
            $choices[$label] = $versionData['version'];
        }

        $builder
            ->add('version', ChoiceType::class, [
                'label' => 'NodeJS Version',
                'choices' => $choices,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NodeJsConfigDto::class,
        ]);
    }
}
