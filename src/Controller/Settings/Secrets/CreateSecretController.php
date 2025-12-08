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

class CreateSecretController extends AbstractController
{
    public function __construct(
        private readonly SecretConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/secrets/new', name: 'settings_secrets_new')]
    public function __invoke(Request $request): Response
    {
        $dto  = new SecretDto();
        $form = $this->createForm(SecretType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the source key
                $key = $this->generateSecretKey($request->request->all()['secret'] ?? []);

                $this->configService->createSecret($key, $dto->toArray());
                $this->addFlash('success', 'Secret created successfully!');

                return $this->redirectToRoute('settings_secrets');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating secret: ' . $e->getMessage());
            }
        }

        return $this->render('settings/secrets/form.html.twig', [
            'page_title'  => 'Add New Secret',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Secrets', 'url' => $this->generateUrl('settings_secrets')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'    => $form,
            'is_edit' => false,
        ]);
    }

    private function generateSecretKey(array $data): string
    {
        // Use source key and group to generate a unique key
        $sourceKey   = $data['sourceKey'] ?? '';
        $sourceGroup = $data['sourceGroup'] ?? '';

        if (!empty($sourceGroup)) {
            $key = ucfirst($sourceGroup) . ucfirst($sourceKey) . 'Secret';
        } else {
            $key = ucfirst($sourceKey) . 'Secret';
        }

        return $key ?: 'Secret_' . uniqid();
    }
}
