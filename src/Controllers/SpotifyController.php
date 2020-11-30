<?php

namespace App\Controllers;

use App\Classes\Spotify;
use App\Traits\ResponseHelper;


class SpotifyController extends BaseController
{
	use ResponseHelper;

	public function albums($request, $response)
	{
		$spotifyCredentials = $this->container->get('spotify_credentials');
		$clientID = $spotifyCredentials['client_id'];
		$secretID = $spotifyCredentials['secret_id'];

		if (($artist = $request->getQueryParam('q', '')) === '') {
			return $this->errorResponse($response, 400, "No search query.");
		}

		try {
			$spotify = new Spotify(
				$clientID,
				$secretID
			);

			$albums = $spotify->getArtistAlbums($artist);
		} catch (\Exception $e) {
			return $this->errorResponse($response, $e->getCode(), $e->getMessage());
		}

		return $response->withJson($albums);
	}
}
