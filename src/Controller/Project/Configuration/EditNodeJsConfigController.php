<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\NodeJsConfigType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditNodeJsConfigController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/configuration/nodejs/edit', name: 'project_configuration_nodejs_edit')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(NodeJsConfigType::class, $projectConfigDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'NodeJS configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating NodeJS configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/nodejs/edit.html.twig', [
            'page_title'  => 'Edit Configuration',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Configuration', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Edit Configuration', 'url' => ''],
            ],
            'form' => $form,
        ]);
    }
}
