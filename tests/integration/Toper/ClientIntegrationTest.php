<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Toper;

use PHPUnit\Framework\TestCase;

class ClientIntegrationTest extends TestCase {
	public const HOST1 = "http://localhost:7820";

	public const HOST2 = "http://localhost:7850";

	public const HOST3 = "http://localhost:7800";

	public const HOST4 = "http://localhost:7900";

	public const HOST_404 = "http://localhost:7844";

	public const HOST_302 = "http://localhost:7832";

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldCallByGetMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request  = $client->get("/request");
		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldBindUrlStringParametersMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());
		$method           = 'get';
		$request          = $client->get("/should_be_{method}", ['method' => $method]);
		$response         = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSendRequestPrams(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/should_have_parameter");
		$request->addQueryParam("key", "value");
		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldReturn4xxResponse(): void {

		$hostPoolProvider = new StaticHostPoolProvider([self::HOST_404]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		$response = $request->send();
		self::assertEquals(404, $response->getStatusCode());
		self::assertEquals('not found', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldReturn3xxResponse(): void {

		$hostPoolProvider = new StaticHostPoolProvider([self::HOST_302]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		$response = $request->send();
		self::assertEquals(302, $response->getStatusCode());
		self::assertEquals('redirect', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldThrowConnectionErrorExceptionIfIsConnectionRefused(): void {
		$hostPoolProvider = new StaticHostPoolProvider(['http://localhost:6788']);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		$this->expectException('\Toper\Exception\ConnectionErrorException');

		$request->send();
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldCallByPostMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->post("/request");

		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldCallNextHostIfFirstFailed(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST2, self::HOST2, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSendPostRequest(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->post("/should_be_post");
		$request->setBody("data");

		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}


	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSendPutRequest(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->put("/should_be_put");
		$request->setBody("data");

		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldSendHeaderSetByGuzzleClientOptions(): void {

		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4, self::HOST1]);
		$client           = new Client(
			$hostPoolProvider,
			new GuzzleClientFactory(
				[
					'request.options' => [
						'headers' => [
							'Content-Type' => 'application/json'
						]
					]
				]
			)
		);

		$request = $client->post("/should_be_post_application_json");
		$request->setBody("data");

		$response = $request->send();
		self::assertEquals(200, $response->getStatusCode());
		self::assertEquals('ok', $response->getBody());
	}
}
