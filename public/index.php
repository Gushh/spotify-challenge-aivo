<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client;
require '../vendor/autoload.php';

$config = require '../src/config/config.php';

$app = new \Slim\App($config);

// /api/v1/
require '../src/routes/api.php';

$app->run();

