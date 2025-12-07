<?php

declare(strict_types=1);

namespace App\Controller\Settings\NodeJs;

use App\Dto\NodeJsConfigDto;
use App\Form\NodeJsConfigType;
use App\Service\Settings\NodeJs\NodeJsConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditNodeJsSettingsController extends AbstractController
{
    public function __construct(
        private readonly NodeJsConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/nodejs/edit', name: 'settings_nodejs_edit')]
    public function __invoke(Request $request): Response
    {
        $nodejsConfig = $this->configService->getNodeJsConfig();
        $dto = NodeJsConfigDto::fromArray($nodejsConfig);

        $form = $this->createForm(NodeJsConfigType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateNodeJsConfig($dto->toArray());
                $this->addFlash('success', 'NodeJS configuration updated successfully!');

                return $this->redirectToRoute('settings_nodejs');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating NodeJS configuration: ' . $e->getMessage());
            }
        }

        return $this->render('settings/nodejs/edit.html.twig', [
            'page_title' => 'Edit NodeJS Configuration',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'NodeJS Configuration', 'url' => $this->generateUrl('settings_nodejs')],
                ['label' => 'Edit', 'url' => ''],
            ],
            'form' => $form,
        ]);
    }
}
