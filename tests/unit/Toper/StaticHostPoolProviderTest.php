<?php

namespace Toper;

use PHPUnit\Framework\TestCase;

class StaticHostPoolProviderTest extends TestCase {
	public const HOST_1 = "http://123.123.123.123";
	public const HOST_2 = "http://234.123.123.123";

	/**
	 * @test
	 */
	public function shouldCreateNewHostPool(): void {
		$hosts    = [self::HOST_1, self::HOST_2];
		$instance = new StaticHostPoolProvider($hosts);

		$hostPool = $instance->get();
		$this->assertHostsArrays($hosts, $hostPool->toArray());
	}

	/**
	 * @param array $hosts1
	 * @param array $hosts2
	 */
	private function assertHostsArrays(array $hosts1, array $hosts2): void {
		sort($hosts1);
		sort($hosts2);
		self::assertEquals($hosts1, $hosts2);
	}
}
