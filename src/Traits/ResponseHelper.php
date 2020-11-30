<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ResponseHelper
{
	private function errorResponse($response, $code, $message)
	{
		$error = [ "error" => [ "status" => $code, "message" => $message ] ];
		
		return $response->withStatus($code)->withJson($error);
	}
}