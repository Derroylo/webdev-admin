<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LogParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly LogParserService $logParserService,
    ) {
    }

    public function __invoke(): Response
    {
        $logTypes     = $this->logParserService->getLogTypes();
        $availability = [];

        foreach (array_keys($logTypes) as $type) {
            $availability[$type] = $this->logParserService->isLogAvailable($type);
        }

        return $this->render('log_viewer/dashboard.html.twig', [
            'log_types'    => $logTypes,
            'availability' => $availability,
        ]);
    }
}
