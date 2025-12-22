<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Dto\GeneralConfigDto;
use App\Form\GeneralConfigType;
use App\Service\Settings\General\GeneralConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditGeneralConfigController extends AbstractController
{
    public function __construct(
        private readonly GeneralConfigServiceInterface $configService,
    ) {
    }

    #[Route('/project/configuration/general/edit', name: 'project_configuration_general_edit')]
    public function __invoke(Request $request): Response
    {
        $generalConfig = $this->configService->getGeneralConfig();
        $dto           = GeneralConfigDto::fromArray($generalConfig);

        $form = $this->createForm(GeneralConfigType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateGeneralConfig($dto->toArray());
                $this->addFlash('success', 'General configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating general configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/general/edit.html.twig', [
            'page_title'  => 'Edit Configuration',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Configuration', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Edit General Configuration', 'url' => ''],
            ],
            'form' => $form,
        ]);
    }
}
