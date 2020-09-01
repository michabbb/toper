<?php
namespace Toper;

interface ClientInterface
{
	/**
	 * @param string $url
	 * @param array  $binds
	 *
	 * @return Request
	 */
    public function get(string $url, array $binds = array()): Request;


	/**
	 * @param string $url
	 * @param array  $binds
	 *
	 * @return Request
	 */
    public function post(string $url, array $binds = array()): Request;

	/**
	 * @param string $url
	 * @param array  $binds
	 *
	 * @return Request
	 */
    public function put(string $url, array $binds = array()): Request;
}
