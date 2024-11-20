<?php

declare(strict_types=1);

namespace App\Controllers;

use Slim\Views\Twig;
use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SpaceXController
{
    public function launches(Request $request, Response $response): Response
    {
        $client = new Client(); 
        $guzzleRequest = $client->request('GET', 'https://api.spacexdata.com/v5/launches');
        $data = json_decode((string) $guzzleRequest->getBody());

        $launchData = array_map(function ($launch) {
            $launchDate = \DateTime::createFromFormat('U', (string) $launch->date_unix);
            return [ 'year' => $launchDate->format('Y'), 'successfulLandings' => $launch->success ? 1 : 0 ];
        }, $data);

        $groupedLaunchData = array_reduce($launchData, function($carry, $item){ 
            if(!isset($carry[$item['year']])){ 
                $carry[$item['year']] = ['year' => $item['year'], 'successfulLandings' => $item['successfulLandings']]; 
            } else { 
                $carry[$item['year']]['successfulLandings'] += $item['successfulLandings']; 
            } 
            return $carry; 
        });
        
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'launches.html.twig', ['launchpadData' => $groupedLaunchData]);
    }

    public function launchSites(Request $request, Response $response): Response
    {
        $client = new Client(); 
        $launchPadRequest = $client->request('GET', 'https://api.spacexdata.com/v4/launchpads');
        $launchPadData = json_decode((string) $launchPadRequest->getBody());

        $launchSiteData = array_map(function ($launchSite) {
            return [ 'name' => $launchSite->full_name, 'launchAttempts' => $launchSite->launch_attempts ];
        }, $launchPadData);
        
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'launch-sites.html.twig', ['launchSiteData' => $launchSiteData]);
    }
}
