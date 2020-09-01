<?php

namespace Toper;

use Guzzle\Http\Client as GuzzleClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {
	/**
	 * @var HostPoolProviderInterface
	 */
	private $hostPoolProvider;

	/**
	 * @var GuzzleClientFactoryInterface
	 */
	private $guzzleClientFactory;

	/**
	 * @var GuzzleClient | MockObject
	 */
	private $guzzleClientMock;

	/**
	 * @var HostPoolInterface
	 */
	private $hostPool;

	public function setUp(): void {
		$this->hostPoolProvider = $this->createHostPoolProviderMock();
		$this->hostPool         = $this->createHostPoolMock();

		$this->hostPoolProvider->expects(self::any())
							   ->method('get')
							   ->willReturn($this->hostPool);

		$this->guzzleClientMock    = $this->createGuzzleClientMock();
		$this->guzzleClientFactory = $this->createGuzzleClientFactoryMock();
	}

	/**
	 * @test
	 */
	public function shouldGetCreateGetRequest(): void {
		$url     = "/test";
		$client  = $this->createClient();
		$request = $client->get($url);

		self::assertEquals($url, $request->getUrl());
		self::assertEquals($this->hostPool, $request->getHostPool());
		self::assertEquals(Request::GET, $request->getMethod());
	}


	/**
	 * @test
	 */
	public function shouldPostCreatePostRequest(): void {
		$url     = "/test";
		$client  = $this->createClient();
		$request = $client->post($url);

		self::assertEquals($url, $request->getUrl());
		self::assertEquals($this->hostPool, $request->getHostPool());
		self::assertEquals(Request::POST, $request->getMethod());
	}


	/**
	 * @test
	 */
	public function shouldPutCreatePostRequest(): void {
		$url     = "/test";
		$client  = $this->createClient();
		$request = $client->put($url);

		self::assertEquals($url, $request->getUrl());
		self::assertEquals($this->hostPool, $request->getHostPool());
		self::assertEquals(Request::PUT, $request->getMethod());
	}

	/**
	 * @test
	 */
	public function shouldGetCreateGetRequestWithBinds(): void {
		$url     = "/test";
		$client  = $this->createClient();
		$binds   = ['key' => 'value'];
		$request = $client->get($url, $binds);

		self::assertEquals($binds, $request->getBinds());
	}


	/**
	 * @test
	 */
	public function shouldPostCreatePostRequestWithBinds(): void {
		$url     = "/test";
		$binds   = ['key' => 'value'];
		$client  = $this->createClient();
		$request = $client->post($url, $binds);

		self::assertEquals($binds, $request->getBinds());
	}


	/**
	 * @test
	 */
	public function shouldPutCreatePostRequestWithBinds(): void {
		$url     = "/test";
		$binds   = ['key' => 'value'];
		$client  = $this->createClient();
		$request = $client->put($url, $binds);

		self::assertEquals($binds, $request->getBinds());
	}


	/**
	 * @return MockObject | HostPoolProviderInterface
	 */
	private function createHostPoolProviderMock() {
		return $this->getMockBuilder('Toper\HostPoolProviderInterface')
					->disableOriginalConstructor()
					->getMock();
	}

	private function createClient(): Client {
		return new Client($this->hostPoolProvider, $this->guzzleClientFactory);
	}

	/**
	 * @return MockObject | HostPoolInterface
	 */
	private function createHostPoolMock() {
		return $this->getMockBuilder('Toper\HostPoolInterface')
					->disableOriginalConstructor()
					->getMock();
	}

	/**
	 * @return MockObject | GuzzleClientFactoryInterface
	 */
	private function createGuzzleClientFactoryMock() {
		$clientFactory = $this->getMockBuilder('Toper\GuzzleClientFactoryInterface')
							  ->getMock();

		$clientFactory->expects(self::any())
					  ->method('create')
					  ->will(self::returnValue($this->guzzleClientMock));

		return $clientFactory;
	}

	/**
	 * @return MockObject | GuzzleClient
	 */
	private function createGuzzleClientMock() {
		return $this->getMockBuilder('Guzzle\Http\Client')
					->disableOriginalConstructor()
					->getMock();
	}
}
