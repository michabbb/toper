<?php

namespace Toper;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Toper\Exception\ConnectionErrorException;
use Toper\Exception\ServerErrorException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\stream_for;


class Request {
	public const GET = "get";

	public const POST = "post";

	public const PUT = "put";

	/**
	 * @var HostPoolInterface
	 */
	private HostPoolInterface $hostPool;

	/**
	 * @var string
	 */
	private string $url;

	/**
	 * @var string
	 */
	private string $method;

	/**
	 * @var GuzzleClientInterface
	 */
	private GuzzleClientInterface $guzzleClient;

	/**
	 * @var string
	 */
	private string $body;

	/**
	 * @var array
	 */
	private array $queryParams = [];

	/**
	 * @var array
	 */
	private array $binds;

	/**
	 * @param string                $method
	 * @param string                $url
	 * @param array                 $binds
	 * @param HostPoolInterface     $hostPool
	 * @param GuzzleClientInterface $guzzleClient
	 */
	public function __construct(
		string $method,
		string $url,
		array $binds,
		HostPoolInterface $hostPool,
		GuzzleClientInterface $guzzleClient
	) {
		$this->method       = $method;
		$this->hostPool     = $hostPool;
		$this->url          = $url;
		$this->guzzleClient = $guzzleClient;
		$this->binds        = $binds;
	}


	/**
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * @return HostPoolInterface
	 */
	public function getHostPool(): HostPoolInterface {
		return $this->hostPool;
	}

	/**
	 * @return string
	 */
	public function getMethod(): string {
		return $this->method;
	}

	/**
	 * @param string $body
	 */
	public function setBody(string $body): void {
		$this->body = $body;
	}

	/**
	 * @return ResponseInterface
	 * @throws Exception\ServerErrorException|ConnectionErrorException
	 *
	 */
	public function send(): ResponseInterface {
		$exception = null;

		while ($this->hostPool->hasNext()) {
			try {
				$guzzleRequest = new GuzzleRequest($this->method, $this->hostPool->getNext().$this->url.build_query($this->queryParams), $this->binds, $this->body);
				return $this->guzzleClient->send($guzzleRequest);
			} catch (ClientException $e) {
				return $e->getResponse();
			} catch (ServerException $e) {
				$exception = new ServerErrorException(
					$e->getResponse(),
					$e->getMessage(),
					$e->getCode(),
					$e
				);
			} catch (GuzzleException $e) {
				$exception = new ConnectionErrorException($e->getMessage(), $e->getCode(), $e);
			}
		}

		throw $exception;
	}

	/**
	 * @return array
	 */
	public function getBinds(): array {
		return $this->binds;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function addQueryParam(string $name, $value): void {
		$this->queryParams[$name] = $value;
	}
}
