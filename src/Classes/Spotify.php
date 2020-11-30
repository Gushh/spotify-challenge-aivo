<?php

namespace App\Classes;

use GuzzleHttp\Client;

class Spotify
{	
	protected $guzzle;
	protected $clientId;
	protected $clientSecret;
	protected $access_token;
	protected $guzzleHeaders;
	protected $albums = [];

	public function __construct($clientId, $clientSecret)
	{
		$this->guzzle = new Client();
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->setAccessToken();

		$this->guzzleHeaders = [
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->access_token,
		];
	}

	public function getArtistAlbums($artist)
	{
		$artistId = $this->getArtistIdbyName($artist);
		$albums = $this->getAlbumsByArtistId($artistId);
		      
		return $albums;	
	}	

	private function setAccessToken()
	{
		$payload = base64_encode($this->clientId . ':' . $this->clientSecret);

		$res = $this->guzzle->post('https://accounts.spotify.com/api/token', [
            'headers' => [
            	'Authorization' => 'Basic ' . $payload
            ],
            'form_params' => [
            	'grant_type' => 'client_credentials'
            ]
        ]);

	   $this->access_token = json_decode($res->getBody())->access_token;
	}

	private function getArtistIdByName($name)
	{
		$res = $this->guzzle->get('https://api.spotify.com/v1/search', [
			'headers' => $this->guzzleHeaders,
			'query' => [
				'q'     => $name,
				'type'  => 'artist',
				'limit' => '1',
			]
		]);

		if (json_decode($res->getBody())->artists->total === 0) {
			throw new \Exception('Artist not found.', 404);
		}

        return json_decode($res->getBody())->artists->items[0]->id;
	}

	private function getAlbumsByArtistId($artistId)
	{
		$res = $this->guzzle->get('https://api.spotify.com/v1/artists/' . $artistId . '/albums', [
			'headers' => $this->guzzleHeaders,
			'query' => [
				'include_groups' => 'album',
				'limit'          => '50',
			]
		]);

		$albums = json_decode($res->getBody())->items;

		foreach ($albums as $album) {
			$this->albums[] = $this->formatAlbumData($album);
		}

		if (empty($this->albums)) {
			throw new \Exception('No albums found.', 404);
		}

        return $this->albums;
	}

	private function formatAlbumData($album)
	{
		return [
			'name'     => $album->name,
			'released' => date("d-m-Y", strtotime($album->release_date)),
			'tracks'   => $album->total_tracks,
			'cover'    => [
				'height' => $album->images[0]->height,
				'width'  => $album->images[0]->width,
				'url'    => $album->images[0]->url,
			]
		];
	}
}