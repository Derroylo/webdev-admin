<?php

declare(strict_types=1);

namespace App\Controller\Logs;

use App\Service\LogParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ViewLogsController extends AbstractController
{
    public function __construct(
        private readonly LogParserService $logParserService
    ) {
    }

    #[Route('/logs/{type}', name: 'app_logs')]
    public function __invoke(string $type): Response
    {
        try {
            $logs = $this->logParserService->parseLogs($type);
            $logTypes = $this->logParserService->getLogTypes();
            
            return $this->render('log_viewer/view.html.twig', [
                'log_type' => $type,
                'log_title' => $logTypes[$type] ?? ucfirst($type),
                'logs' => $logs,
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_dashboard');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_dashboard');
        }
    }
}
