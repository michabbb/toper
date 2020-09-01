<?php

namespace Toper;

interface HostPoolInterface {
	/**
	 * @return string
	 */
	public function getNext(): string;

	/**
	 * @return boolean
	 */
	public function hasNext(): bool;

	/**
	 * @return array
	 */
	public function toArray(): array;
}
