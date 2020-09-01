<?php

namespace Toper;

use Closure;
use Exception;
use Guzzle\Common\Collection;
use Guzzle\Http\ClientInterface as GuzzleClientInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request as GuzzleRequest;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Http\QueryString;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Toper\Exception\ServerErrorException;

class RequestTest extends TestCase {
	public const URL = "/test";

	public const BASE_URL1 = "http://123.123.123.123";

	public const BASE_URL2 = "http://123.123.123.124";

	private $hostPool;

	protected function setUp(): void {
		$this->hostPool = new SimpleHostPool([self::BASE_URL1]);
	}

	/**
	 * @test
	 * @throws ServerErrorException
	 */
	public function shouldSendRequest(): void {
		$guzzleClient = $this->createGuzzleClientMock();

		$guzzleResponse = new GuzzleResponse(200, [], 'ok');

		$guzzleRequest = $this->createGuzzleRequest($guzzleResponse);

		$this->prepareGuzzleClientMock($guzzleClient, $guzzleRequest);

		self::returnValue($guzzleResponse);

		$instance = $this->createInstance(
			Request::GET,
			[],
			$guzzleClient
		);
		$response = $instance->send();

		self::assertEquals($guzzleResponse->getStatusCode(), $response->getStatusCode());
		self::assertEquals($guzzleResponse->getBody(true), $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSendRequestWithBinds(): void {
		$guzzleClient = $this->createGuzzleClientMock();

		$binds = ['key' => 'value'];

		$guzzleResponse = new GuzzleResponse(200, [], 'ok');

		$guzzleRequest = $this->createGuzzleRequest($guzzleResponse);

		$this->prepareGuzzleClientMock($guzzleClient, $guzzleRequest, Request::GET, $binds);

		$instance = $this->createInstance(
			Request::GET,
			$binds,
			$guzzleClient
		);

		$instance->send();
	}


	/**
	 * @test
	 * @noinspection DuplicatedCode
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSetPostBodyIfRequestIsPost(): void {
		$body           = "some body";
		$guzzleClient1  = $this->createGuzzleClientMock();
		$this->hostPool = new SimpleHostPool([self::BASE_URL1]);

		$guzzleResponse = new GuzzleResponse(200, [], 'ok');

		$guzzleRequest = $this->createGuzzleEntityEnclosingRequest($guzzleResponse);

		$this->prepareGuzzleClientMock(
			$guzzleClient1,
			$guzzleRequest,
			Request::POST
		);

		$guzzleRequest->expects(self::once())
					  ->method('setBody')
					  ->with($body);

		$instance = $this->createInstance(Request::POST, [], $guzzleClient1);
		$instance->setBody($body);

		$instance->send();
	}


	/**
	 * @test
	 * @noinspection DuplicatedCode
	 */
	public function shouldSetBodyIfRequestIsPut(): void {
		$body           = "some body";
		$guzzleClient   = $this->createGuzzleClientMock();
		$this->hostPool = new SimpleHostPool([self::BASE_URL1]);

		$guzzleResponse = new GuzzleResponse(200, [], 'ok');

		$guzzleRequest = $this->createGuzzleEntityEnclosingRequest($guzzleResponse);

		$this->prepareGuzzleClientMock($guzzleClient, $guzzleRequest, Request::PUT);

		$guzzleRequest->expects(self::once())
					  ->method('setBody')
					  ->with($body);

		$instance = $this->createInstance(Request::PUT, [], $guzzleClient);
		$instance->setBody($body);


		$instance->send();
	}

	/**
	 * @test
	 */
	public function shouldReturnResponseIfGuzzleThrowsClientErrorResponseException(): void {
		$responseErrorCode = 404;
		$responseBody      = 'not found';

		$guzzleClient   = $this->createGuzzleClientMock();
		$this->hostPool = new SimpleHostPool([self::BASE_URL1]);

		$guzzleResponse = new GuzzleResponse($responseErrorCode, [], $responseBody);

		$clientErrorResponseException = new ClientErrorResponseException();
		$clientErrorResponseException->setResponse($guzzleResponse);

		$e             = $this->createGuzzleClientException($guzzleResponse);
		$guzzleRequest = $this->createGuzzleRequestMockWithException($e);
		$this->prepareGuzzleClientMock($guzzleClient, $guzzleRequest);

		$instance = $this->createInstance(Request::GET, [], $guzzleClient);

		$result = $instance->send();
		self::assertEquals($responseErrorCode, $result->getStatusCode());
		self::assertEquals($responseBody, $result->getBody());
	}

	/**
	 * @test
	 */
	public function shouldSetQueryParam(): void {
		$paramName1  = 'name';
		$paramValue1 = 'value';

		$paramName2  = 'name';
		$paramValue2 = 'value';

		$responseErrorCode = 404;
		$responseBody      = 'not found';

		$guzzleClient   = $this->createGuzzleClientMock();
		$this->hostPool = new SimpleHostPool([self::BASE_URL1]);

		$guzzleResponse = new GuzzleResponse($responseErrorCode, [], $responseBody);

		$clientErrorResponseException = new ClientErrorResponseException();
		$clientErrorResponseException->setResponse($guzzleResponse);

		$guzzleRequest = $this->createGuzzleRequest($guzzleResponse);
		$this->prepareGuzzleClientMock($guzzleClient, $guzzleRequest);

		$instance = $this->createInstance(Request::GET, [], $guzzleClient);
		$instance->addQueryParam($paramName1, $paramValue1);
		$instance->addQueryParam($paramName2, $paramValue2);

		$instance->send();

		self::assertEquals($paramValue1, $guzzleRequest->getQuery()->get($paramName1));
		self::assertEquals($paramValue2, $guzzleRequest->getQuery()->get($paramName2));
	}

	/**
	 * @test
	 */
	public function shouldReturnBinds(): void {
		$binds    = ['key' => 'value'];
		$instance = $this->createInstance(
			Request::GET,
			$binds,
			$this->createGuzzleClientMock()
		);

		self::assertEquals($binds, $instance->getBinds());
	}

	/**
	 * @param string                $method
	 * @param array                 $binds
	 * @param GuzzleClientInterface $guzzleClient
	 *
	 * @return Request
	 */
	private function createInstance(
		string $method,
		array $binds,
		GuzzleClientInterface $guzzleClient
	): Request {
		return new Request(
			$method,
			self::URL,
			$binds,
			$this->hostPool,
			$guzzleClient
		);
	}

	/**
	 * @return MockObject | GuzzleClientInterface
	 */
	private function createGuzzleClientMock() {
		return $this->getMockBuilder('Guzzle\Http\Client')
					->disableOriginalConstructor()
					->getMock();
	}

	/**
	 * @param GuzzleResponse $guzzleResponse
	 *
	 * @return GuzzleRequest | MockObject
	 */
	private function createGuzzleRequest(GuzzleResponse $guzzleResponse) {

		$guzzleRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
							  ->disableOriginalConstructor()
							  ->getMock();

		$guzzleRequest->expects(self::once())
					  ->method('send')
					  ->willReturn($guzzleResponse);

		$guzzleQueryParams = new QueryString();
		$guzzleRequest->expects(self::any())
					  ->method('getQuery')
					  ->willReturn($guzzleQueryParams);

		return $guzzleRequest;
	}

	/**
	 * @param Exception $e
	 *
	 * @return GuzzleRequest | MockObject
	 */
	private function createGuzzleRequestMockWithException(Exception $e) {
		$guzzleRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
							  ->disableOriginalConstructor()
							  ->getMock();

		$guzzleRequest->expects(self::once())
					  ->method('send')
					  ->will(self::throwException($e));

		return $guzzleRequest;
	}


	/**
	 * @param GuzzleResponse $guzzleResponse
	 *
	 * @return EntityEnclosingRequest | MockObject
	 */
	private function createGuzzleEntityEnclosingRequest(GuzzleResponse $guzzleResponse) {
		$guzzleParams  = new Collection();
		$guzzleRequest = $this->getMockBuilder('Guzzle\Http\Message\EntityEnclosingRequest')
							  ->disableOriginalConstructor()
							  ->getMock();

		$guzzleRequest->expects(self::once())
					  ->method('send')
					  ->willReturn($guzzleResponse);

		$guzzleRequest->expects(self::any())
					  ->method('getParams')
					  ->willReturn($guzzleParams);

		return $guzzleRequest;
	}

	/**
	 * @param MockObject    $guzzleClient
	 * @param GuzzleRequest $guzzleRequest
	 * @param string        $method
	 * @param array         $binds
	 */
	private function prepareGuzzleClientMock(
		MockObject $guzzleClient,
		GuzzleRequest $guzzleRequest,
		$method = Request::GET,
		array $binds = []
	): void {
		$guzzleClient->expects(self::any())
					 ->method($method)
					 ->with([self::URL, $binds])
					 ->willReturn($guzzleRequest);
	}

	/**
	 * @param MockObject $mockObject
	 * @param string     $method
	 * @param Closure    $callback
	 *
	 * @return MockObject | GuzzleClientInterface
	 */
	private function mockGuzzleClientMethod(
		MockObject $mockObject,
		string $method,
		Closure $callback
	) {
		$mockObject->expects(self::any())
				   ->method($method)
				   ->willReturnCallback($callback);

		return $mockObject;
	}

	/**
	 * @return ServerErrorResponseException
	 */
	private function createGuzzleServerErrorResponseException(): ServerErrorResponseException {
		$e        = new ServerErrorResponseException();
		$response = new GuzzleResponse(500);
		$e->setResponse($response);

		return $e;
	}

	private function createGuzzleCurlException(): CurlException {
		return new CurlException();
	}


	/**
	 * @param GuzzleResponse $response
	 *
	 * @return ClientErrorResponseException
	 */
	private function createGuzzleClientException(GuzzleResponse $response): ClientErrorResponseException {
		$e = new ClientErrorResponseException();

		$e->setResponse($response);

		return $e;
	}
}
