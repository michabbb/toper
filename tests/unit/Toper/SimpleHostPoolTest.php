<?php

namespace Toper;

use PHPUnit\Framework\TestCase;

class SimpleHostPoolTest extends TestCase {
	public const HOST_1 = "http://123.232.232.123";
	public const HOST_2 = "http://222.232.232.123";

	/**
	 * @test
	 * @throws Exception\NextHostException
	 */
	public function shouldGetNext(): void {
		$hostPool = $this->createInstance(
			[self::HOST_1, self::HOST_2]
		);

		self::assertEquals(self::HOST_1, $hostPool->getNext());
		self::assertEquals(self::HOST_2, $hostPool->getNext());
	}

	/**
	 * @test
	 * @throws Exception\NextHostException
	 */
	public function shouldHaveNext(): void {
		$hostPool = $this->createInstance(
			[self::HOST_1, self::HOST_2]
		);

		self::assertTrue($hostPool->hasNext());
		$hostPool->getNext();
		self::assertTrue($hostPool->hasNext());
	}

	/**
	 * @test
	 * @throws Exception\NextHostException
	 */
	public function shouldNotHaveNext(): void {
		$hostPool = $this->createInstance(
			[self::HOST_1]
		);

		$hostPool->getNext();
		self::assertFalse($hostPool->hasNext());
	}

	/**
	 * @test
	 * @throws Exception\NextHostException
	 * @throws Exception\NextHostException
	 */
	public function shouldThrowExceptionIfThereIsNoNextHost(): void {
		$hostPool = $this->createInstance(
			[self::HOST_1]
		);

		$hostPool->getNext();

		$this->expectException('Toper\Exception\NextHostException');
		$hostPool->getNext();
	}

	/**
	 * @param array $hosts
	 *
	 * @return SimpleHostPool
	 */
	private function createInstance(array $hosts): SimpleHostPool {
		return new SimpleHostPool($hosts);
	}
}
