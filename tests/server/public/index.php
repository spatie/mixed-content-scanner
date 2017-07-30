<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->get('/', function () {
    return view('home');
});

$app->get('/mixedContent', function () {
    return view('mixedContent');
});

$app->get('/noMixedContent', function () {
    return view('noMixedContent');
});

$app->get('booted', function () {
    return 'app has booted';
});

$app->run();
