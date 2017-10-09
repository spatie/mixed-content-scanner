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

$app->get('/linkRelStyleSheet', function () {
    return view('linkRelStyleSheet');
});

$app->get('/linkRelProfile', function () {
    return view('linkRelProfile');
});

$app->get('noResponse', function () {
    die();
});

$app->get('redirect', function () {
    return redirect('/noMixedContent');
});

$app->get('booted', function () {
    return 'app has booted';
});

$app->run();
