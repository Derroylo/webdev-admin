<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\PhpConfigType;
use App\Service\Config\PhpPresetsServiceInterface;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditPhpConfigController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
        private readonly PhpPresetsServiceInterface $phpPresetsService,
    ) {
    }

    #[Route('/project/configuration/php/edit', name: 'project_configuration_php_edit')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(PhpConfigType::class, $projectConfigDto);
        $form->handleRequest($request);
        
        // After handleRequest, if form is not submitted, repopulate unmapped collections
        // handleRequest() resets unmapped fields, so we need to set them again
        if (!$form->isSubmitted()) {
            $phpConfig = $projectConfigDto->php;
            
            // Repopulate config collection
            if (is_array($phpConfig->config) && !empty($phpConfig->config)) {
                $formData = [];
                foreach ($phpConfig->config as $settingName => $value) {
                    $formData[] = [
                        'settingName' => $settingName,
                        'value'       => $value,
                    ];
                }
                $form->get('config')->setData($formData);
            }
            
            // Repopulate configWeb collection
            if (is_array($phpConfig->configWeb) && !empty($phpConfig->configWeb)) {
                $formWebData = [];
                foreach ($phpConfig->configWeb as $settingName => $value) {
                    $formWebData[] = [
                        'settingName' => $settingName,
                        'value'       => $value,
                    ];
                }
                $form->get('configWeb')->setData($formWebData);
            }
            
            // Repopulate configCLI collection
            if (is_array($phpConfig->configCLI) && !empty($phpConfig->configCLI)) {
                $formCLIData = [];
                foreach ($phpConfig->configCLI as $settingName => $value) {
                    $formCLIData[] = [
                        'settingName' => $settingName,
                        'value'       => $value,
                    ];
                }
                $form->get('configCLI')->setData($formCLIData);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'PHP configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating PHP configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/php/edit.html.twig', [
            'page_title'   => 'Edit Configuration',
            'breadcrumbs'  => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Configuration', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Edit Configuration', 'url' => ''],
            ],
            'form'                      => $form,
            'recommended_settings_groups' => $this->phpPresetsService->getRecommendedSettingsGroups(),
        ]);
    }
}
