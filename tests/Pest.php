<?php

use Spatie\MixedContentScanner\Test\Server;

uses()->beforeEach(function () {
    Server::boot();
})->in(__DIR__);
