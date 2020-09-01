<?php

namespace Toper\Exception;

use Guzzle\Http\Message\Response;

class ServerErrorException extends RequestException {
	/**
	 * @var Response $response
	 */
	private $response;

	/**
	 * @param Response $response
	 * @param int                           $message
	 * @param int                           $code
	 * @param                               $previous
	 */
	public function __construct(Response $response, int $message, int $code, $previous) {
		parent::__construct($message, $code, $previous);

		/** @noinspection UnusedConstructorDependenciesInspection */
		$this->response = $response;
	}
}
