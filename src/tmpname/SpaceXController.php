<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SpaceXService;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SpaceXController
{
    public function launches(Request $request, Response $response): Response
    {
        $launchData = SpaceXService::getLaunchData();
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'launches.html.twig', ['launchpadData' => $launchData]);
    }

    public function launchSites(Request $request, Response $response): Response
    {
        $launchSiteData = SpaceXService::getLaunchSiteData();
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'launch-sites.html.twig', ['launchSiteData' => $launchSiteData]);
    }
}
