<?php

namespace Toper;

use Guzzle\Http\Client as GuzzleClient;

class GuzzleClientFactory implements GuzzleClientFactoryInterface {
	/**
	 * @var array
	 */
	private $guzzleClientOptions;

	/**
	 * @param array $guzzleClientOptions
	 */
	public function __construct(array $guzzleClientOptions = []) {
		$this->guzzleClientOptions = $guzzleClientOptions;
	}

	/**
	 * @return GuzzleClient
	 */
	public function create(): GuzzleClient {
		return new GuzzleClient('', $this->guzzleClientOptions);
	}
}
