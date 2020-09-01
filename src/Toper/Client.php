<?php

namespace Toper;

class Client implements ClientInterface {
	/**
	 * @var HostPoolProviderInterface
	 */
	private $hostPoolProvider;

	/**
	 * @var GuzzleClientFactoryInterface
	 */
	private $guzzleClientFactory;

	/**
	 * @param HostPoolProviderInterface    $hostPoolProvider
	 * @param GuzzleClientFactoryInterface $guzzleClientFactory
	 */
	public function __construct(
		HostPoolProviderInterface $hostPoolProvider,
		GuzzleClientFactoryInterface $guzzleClientFactory
	) {
		$this->hostPoolProvider    = $hostPoolProvider;
		$this->guzzleClientFactory = $guzzleClientFactory;
	}

	/**
	 * @param string $url
	 *
	 * @param array  $binds
	 *
	 * @return Request
	 */
	public function get(string $url, array $binds = []): Request {
		return new Request(
			Request::GET,
			$url,
			$binds,
			$this->hostPoolProvider->get(),
			$this->guzzleClientFactory->create()
		);
	}

	/**
	 * @param string $url
	 *
	 * @param array  $binds
	 *
	 * @return Request
	 */
	public function post(string $url, array $binds = []): Request {
		return new Request(
			Request::POST,
			$url,
			$binds,
			$this->hostPoolProvider->get(),
			$this->guzzleClientFactory->create()
		);
	}


	/**
	 * @param string $url
	 *
	 * @param array  $binds
	 *
	 * @return Request
	 */
	public function put(string $url, array $binds = []): Request {
		return new Request(
			Request::PUT,
			$url,
			$binds,
			$this->hostPoolProvider->get(),
			$this->guzzleClientFactory->create()
		);
	}
}
