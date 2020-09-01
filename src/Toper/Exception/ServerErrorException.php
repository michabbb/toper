<?php

namespace Toper\Exception;

use Psr\Http\Message\ResponseInterface;

class ServerErrorException extends RequestException {
	/**
	 * @var ResponseInterface $response
	 */
	private ResponseInterface $response;

	/**
	 * @param ResponseInterface             $response
	 * @param int                           $message
	 * @param int                           $code
	 * @param                               $previous
	 */
	public function __construct(ResponseInterface $response, int $message, int $code, $previous) {
		parent::__construct($message, $code, $previous);

		/** @noinspection UnusedConstructorDependenciesInspection */
		$this->response = $response;
	}
}
