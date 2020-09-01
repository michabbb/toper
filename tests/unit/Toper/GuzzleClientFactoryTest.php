<?php

namespace Toper;

use PHPUnit\Framework\TestCase;

class GuzzleClientFactoryTest extends TestCase {
	public const BASE_URL = "http://123.123.123.123";

	/**
	 * @var array
	 */
	private $options = [
		'timeout' => 12
	];

	/**
	 * @test
	 */
	public function shouldCreateClient(): void {
		$factory = new GuzzleClientFactory($this->options);
		$client = $factory->create();
		self::assertEquals($this->options['timeout'], $client->getConfig('timeout'));
	}
}
