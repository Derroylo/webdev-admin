<?php

declare(strict_types=1);

namespace App\Controller\Settings\Secrets;

use App\Dto\SecretDto;
use App\Form\SecretType;
use App\Service\Settings\Secrets\SecretConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditSecretController extends AbstractController
{
    public function __construct(
        private readonly SecretConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/secrets/{key}/edit', name: 'settings_secrets_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $secret = $this->configService->getSecret($key);
        
        if (!$secret) {
            $this->addFlash('danger', 'Secret not found.');
            return $this->redirectToRoute('settings_secrets');
        }

        $dto = SecretDto::fromArray($secret);
        $form = $this->createForm(SecretType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateSecret($key, $dto->toArray());
                $this->addFlash('success', 'Secret updated successfully!');

                return $this->redirectToRoute('settings_secrets');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating secret: ' . $e->getMessage());
            }
        }

        return $this->render('settings/secrets/form.html.twig', [
            'page_title' => 'Edit Secret: ' . $key,
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Secrets', 'url' => $this->generateUrl('settings_secrets')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form' => $form,
            'is_edit' => true,
            'secret_key' => $key,
        ]);
    }
}

