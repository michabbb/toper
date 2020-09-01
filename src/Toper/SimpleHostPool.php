<?php

namespace Toper;

use Toper\Exception\NextHostException;

class SimpleHostPool implements HostPoolInterface {
	/**
	 * @var array
	 */
	private $hosts;

	/**
	 * @var int
	 */
	private $index = 0;

	/**
	 * @param array $hosts
	 */
	public function __construct(array $hosts) {
		$this->hosts = $hosts;
	}


	/**
	 * @return string
	 * @throws NextHostException
	 */
	public function getNext(): string {
		if (!isset($this->hosts[$this->index])) {
			/** @noinspection ThrowRawExceptionInspection */
			throw new NextHostException();
		}

		return $this->hosts[$this->index++];
	}

	/**
	 * @return bool
	 */
	public function hasNext(): bool {
		return isset($this->hosts[$this->index]);
	}

	/**
	 * @return array
	 */
	public function toArray(): array {
		return $this->hosts;
	}
}
