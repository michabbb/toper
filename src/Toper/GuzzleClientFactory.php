<?php

namespace Toper;

class GuzzleClientFactory implements GuzzleClientFactoryInterface {
	/**
	 * @var array
	 */
	private array $guzzleClientOptions;

	/**
	 * @param array $guzzleClientOptions
	 */
	public function __construct(array $guzzleClientOptions = []) {
		$this->guzzleClientOptions = $guzzleClientOptions;
	}

	/**
	 * @return \GuzzleHttp\Client
	 */
	public function create(): \GuzzleHttp\Client {
		return new \GuzzleHttp\Client($this->guzzleClientOptions);
	}
}
