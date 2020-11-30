<?php 

use App\Controllers\SpotifyController;

$app->group('/api/v1/', function ($app) {
    $app->get('albums', SpotifyController::class . ':albums');
});

