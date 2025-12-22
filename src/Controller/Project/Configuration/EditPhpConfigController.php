<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Dto\PhpConfigDto;
use App\Form\PhpConfigType;
use App\Service\Settings\Php\PhpConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditPhpConfigController extends AbstractController
{
    public function __construct(
        private readonly PhpConfigServiceInterface $configService,
    ) {
    }

    #[Route('/project/configuration/php/edit', name: 'project_configuration_php_edit')]
    public function __invoke(Request $request): Response
    {
        $phpConfig = $this->configService->getPhpConfig();
        $dto       = PhpConfigDto::fromArray($phpConfig);

        $form = $this->createForm(PhpConfigType::class, $dto);

        // Set unmapped field values
        if (isset($phpConfig['config']['opcache.enable'])) {
            $form->get('opcache_enable')->setData($phpConfig['config']['opcache.enable']);
        }

        if (isset($phpConfig['config']['xdebug.mode'])) {
            $form->get('xdebug_mode')->setData($phpConfig['config']['xdebug.mode']);
        }

        if (isset($phpConfig['config']['xdebug.start_with_request'])) {
            $form->get('xdebug_start_with_request')->setData($phpConfig['config']['xdebug.start_with_request']);
        }

        if (isset($phpConfig['configWeb']['memory_limit'])) {
            $form->get('memory_limit_web')->setData($phpConfig['configWeb']['memory_limit']);
        }

        if (isset($phpConfig['configCLI']['memory_limit'])) {
            $form->get('memory_limit_cli')->setData($phpConfig['configCLI']['memory_limit']);
        }

        if (isset($phpConfig['configCLI']['max_execution_time'])) {
            $form->get('max_execution_time_cli')->setData($phpConfig['configCLI']['max_execution_time']);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Build config array from form data
                $data = [
                    'version'   => $dto->version,
                    'config'    => [],
                    'configWeb' => [],
                    'configCLI' => [],
                ];

                if ($form->get('opcache_enable')->getData()) {
                    $data['config']['opcache.enable'] = $form->get('opcache_enable')->getData();
                }

                if ($form->get('xdebug_mode')->getData()) {
                    $data['config']['xdebug.mode'] = $form->get('xdebug_mode')->getData();
                }

                if ($form->get('xdebug_start_with_request')->getData()) {
                    $data['config']['xdebug.start_with_request'] = $form->get('xdebug_start_with_request')->getData();
                }

                if ($form->get('memory_limit_web')->getData()) {
                    $data['configWeb']['memory_limit'] = $form->get('memory_limit_web')->getData();
                }

                if ($form->get('memory_limit_cli')->getData()) {
                    $data['configCLI']['memory_limit'] = $form->get('memory_limit_cli')->getData();
                }

                if ($form->get('max_execution_time_cli')->getData()) {
                    $data['configCLI']['max_execution_time'] = $form->get('max_execution_time_cli')->getData();
                }

                $this->configService->updatePhpConfig($data);
                $this->addFlash('success', 'PHP configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating PHP configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/php/edit.html.twig', [
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
