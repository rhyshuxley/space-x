<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', \App\Controllers\SpaceXController::class . ':launches');
$app->get('/launch-sites', \App\Controllers\SpaceXController::class . ':launchSites');

$app->run();
