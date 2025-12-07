<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 10)]
class ProjectSessionListener
{
    /**
     * Routes that don't require a project to be selected
     */
    private const EXCLUDED_ROUTES = [
        'project_select',
        'project_list',
        'project_browse',
        'project_current',
        'project_clear',
        'app_dashboard',
        'app_logs',
        'app_api_logs',
        'placeholder',
        '_wdt',
        '_profiler',
        '_profiler_home',
        '_profiler_search',
        '_profiler_search_bar',
        '_profiler_phpinfo',
        '_profiler_xdebug',
        '_profiler_font',
        '_profiler_search_results',
        '_profiler_open_file',
        '_profiler_router',
        '_profiler_exception',
        '_profiler_exception_css',
    ];

    /**
     * Route prefixes that don't require a project
     */
    private const EXCLUDED_PREFIXES = [
        '_wdt',
        '_profiler',
        '_error',
    ];

    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Skip excluded routes
        if ($route && in_array($route, self::EXCLUDED_ROUTES, true)) {
            return;
        }

        // Skip excluded prefixes
        if ($route) {
            foreach (self::EXCLUDED_PREFIXES as $prefix) {
                if (str_starts_with($route, $prefix)) {
                    return;
                }
            }
        }

        // Skip asset requests
        $pathInfo = $request->getPathInfo();
        if (str_starts_with($pathInfo, '/build/') || 
            str_starts_with($pathInfo, '/assets/') ||
            str_starts_with($pathInfo, '/bundles/')) {
            return;
        }

        // Check if project is selected for API requests
        if ($request->isXmlHttpRequest() || str_starts_with($pathInfo, '/api/')) {
            if (!$this->projectSessionService->hasProjectSelected()) {
                // Return JSON error for API requests
                $event->setResponse(new JsonResponse([
                    'error' => 'No project selected',
                    'code' => 'PROJECT_NOT_SELECTED',
                ], Response::HTTP_BAD_REQUEST));
            }
        }
        
        // For regular page requests, the modal will be shown via JavaScript
        // when project_selected is false (handled by Twig global variables)
    }
}

