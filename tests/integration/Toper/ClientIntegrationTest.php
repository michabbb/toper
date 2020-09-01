<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Toper;

use PHPUnit\Framework\TestCase;
use Toper\Exception\ConnectionErrorException;

class ClientIntegrationTest extends TestCase {
	public const HOST1 = "http://localhost:7820";

	public const HOST2 = "http://localhost:7850";

	public const HOST3 = "http://localhost:7800";

	public const HOST4 = "http://localhost:7900";

	public const HOST_404 = "http://localhost:7844";

	public const HOST_302 = "http://localhost:7832";

	/**
	 * @test
	 */
	public function shouldCallByGetMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request  = $client->get("/request");
		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (Exception\ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldBindUrlStringParametersMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());
		$method           = 'get';
		$request          = $client->get("/should_be_{method}", ['method' => $method]);
		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (Exception\ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldSendRequestPrams(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/should_have_parameter");
		$request->addQueryParam("key", "value");
		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (Exception\ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldReturn4xxResponse(): void {

		$hostPoolProvider = new StaticHostPoolProvider([self::HOST_404]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		try {
			$response = $request->send();
			self::assertEquals(404, $response->getStatusCode());
			self::assertEquals('not found', $response->getBody());
		} catch (Exception\ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldReturn3xxResponse(): void {

		$hostPoolProvider = new StaticHostPoolProvider([self::HOST_302]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		try {
			$response = $request->send();
			self::assertEquals(302, $response->getStatusCode());
			self::assertEquals('redirect', $response->getBody());
		} catch (Exception\ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 * @throws Exception\ServerErrorException
	 */
	public function shouldThrowConnectionErrorExceptionIfIsConnectionRefused(): void {
		$hostPoolProvider = new StaticHostPoolProvider(['http://localhost:6788']);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		$this->expectException(ConnectionErrorException::class);

		$request->send();
	}

	/**
	 * @test
	 */
	public function shouldCallByPostMethod(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->post("/request");

		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldCallNextHostIfFirstFailed(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST2, self::HOST2, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->get("/request");

		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
	 */
	public function shouldSendPostRequest(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->post("/should_be_post");
		$request->setBody("data");

		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}


	/**
	 * @test
	 */
	public function shouldSendPutRequest(): void {
		$hostPoolProvider = new StaticHostPoolProvider([self::HOST4, self::HOST1]);
		$client           = new Client($hostPoolProvider, new GuzzleClientFactory());

		$request = $client->put("/should_be_put");
		$request->setBody("data");

		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}

	/**
	 * @test
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

		try {
			$response = $request->send();
			self::assertEquals(200, $response->getStatusCode());
			self::assertEquals('ok', $response->getBody());
		} catch (ConnectionErrorException $e) {
		} catch (Exception\ServerErrorException $e) {
		}
	}
}
