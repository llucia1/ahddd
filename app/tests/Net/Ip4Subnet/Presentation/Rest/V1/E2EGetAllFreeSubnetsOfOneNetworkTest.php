<?php
declare(strict_types=1);

namespace Net\Ip4Subnet\Presentation\Rest\V1;



use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;


use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
class E2EGetAllFreeSubnetsOfOneNetworkTest extends TestCase
{
    private const ERROR = 'Error';
    private const BEARER = 'Bearer';
    private const URI = '/v1/ip4/subnet/network';
    private const TOKEN_PATH = '/api/v1/auth/login';

    private static Client $client;
    private static string $token;
    private static string $url;
    private static string $uuid;

    public static function setUpBeforeClass(): void
    {
        $faker = \Faker\Factory::create();

        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_OK, ['token' => 'fake_token']),
            self::createMockResponse(HttpResponse::HTTP_OK, [
                ['ip' => $faker->ipv4(), 'mask' => 24],
                ['ip' => $faker->ipv4(), 'mask' => 28],
            ]),
            self::createMockResponse(HttpResponse::HTTP_NOT_FOUND, ['error' => 'Network not found.']),
            self::createMockResponse(HttpResponse::HTTP_BAD_REQUEST, ['error' => 'Invalid mask parameter.']),
            self::createMockResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, ['error' => 'Unexpected error occurred.']),
        ]);

        self::initializeClient($mock);

        $response = self::$client->post(self::TOKEN_PATH, [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'password',
            ],
        ]);

        $data = json_decode((string)$response->getBody(), true);
        self::$token = $data['token'];

        self::$uuid = $faker->uuid();
        self::$url = self::URI . '/' . self::$uuid . '/free';
    }

    private static function createMockResponse(int $status, array $data = []): Response
    {
        return new Response($status, [], json_encode($data));
    }

    private static function initializeClient(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => 'http://localhost:80']);
    }

    private static function makeRequest(string $mask): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->get(self::$url . '?mask=' . $mask, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token,
                ],
            ]);
        } catch (\Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }

    public static function testGetAllFreeSubnetsSuccess(): void
    {
        $response = self::makeRequest('24');
        self::assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        self::assertIsArray($data);

        foreach ($data as $subnet) {
            self::assertArrayHasKey('ip', $subnet);
            self::assertArrayHasKey('mask', $subnet);
            self::assertIsString($subnet['ip']);
            self::assertIsInt($subnet['mask']);
        }
    }

    public static function testGetAllFreeSubnetsNetworkNotFound(): void
    {
        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_NOT_FOUND, ['error' => 'Network not found.']),
        ]);
        self::initializeClient($mock);

        $response = self::makeRequest('24');
        self::assertEquals(HttpResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testGetAllFreeSubnetsInvalidMask(): void
    {
        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_BAD_REQUEST, ['error' => 'Invalid mask parameter.']),
        ]);
        self::initializeClient($mock);

        $response = self::makeRequest('invalid-mask');
        self::assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testGetAllFreeSubnetsServerError(): void
    {
        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, ['error' => 'Unexpected error occurred.']),
        ]);
        self::initializeClient($mock);

        $response = self::makeRequest('24');
        self::assertEquals(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // php bin/phpunit tests/Net/Ip4Subnet/Presentation/Rest/V1/E2EGetAllFreeSubnetsOfOneNetworkTest.php
}





