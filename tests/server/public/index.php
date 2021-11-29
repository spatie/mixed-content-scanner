<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->router->get('/', function () {
    return view('home');
});

$app->router->get('/mixedContent', function () {
    return view('mixedContent');
});

$app->router->get('/noMixedContent', function () {
    return view('noMixedContent');
});

$app->router->get('/linkRelStyleSheet', function () {
    return view('linkRelStyleSheet');
});

$app->router->get('/linkRelProfile', function () {
    return view('linkRelProfile');
});

$app->router->get('noResponse', function () {
    die();
});

$app->router->get('redirect', function () {
    return redirect('/noMixedContent');
});

$app->router->get('booted', function () {
    return 'app has booted';
});

$app->run();
