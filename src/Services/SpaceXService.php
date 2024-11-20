<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class SpaceXService
{
    public static function getLaunchData(): array
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

        return $groupedLaunchData;
    }

    public static function getLaunchSiteData(): array
    {
        $client = new Client(); 
        $launchPadRequest = $client->request('GET', 'https://api.spacexdata.com/v4/launchpads');
        $launchPadData = json_decode((string) $launchPadRequest->getBody());

        $launchSiteData = array_map(function ($launchSite) {
            return [ 'name' => $launchSite->full_name, 'launchAttempts' => $launchSite->launch_attempts ];
        }, $launchPadData);

        return $launchSiteData;
    }
}
