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
class E2EGetAllSubnetsTest extends TestCase
{
    private const ERROR = 'Error';
    private const BEARER = 'Bearer';
    private const URI = '/v1/ip4/subnet';
    private const TOKEN_PATH = '/api/v1/auth/login';

    private static Client $client;
    private static string $token;
    private static string $url;

    public static function setUpBeforeClass(): void
    {
        $faker = \Faker\Factory::create();

        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_OK, ['token' => 'fake_token']),
            self::createMockResponse(HttpResponse::HTTP_OK, [
                [
                    'uuid' => $faker->uuid(),
                    'ip' => $faker->ipv4(),
                    'mask' => 24,
                    'floatgroupUuid' => $faker->uuid(),
                    'owner' => 'User1',
                ],
                [
                    'uuid' => $faker->uuid(),
                    'ip' => $faker->ipv4(),
                    'mask' => 28,
                    'floatgroupUuid' => $faker->uuid(),
                    'owner' => 'User2',
                ],
            ]),
            self::createMockResponse(HttpResponse::HTTP_NOT_FOUND, ['error' => 'No subnets found.']),
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
        self::$url = self::URI;
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

    private static function makeRequest(): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->get(self::$url, [
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

    public static function testGetAllSubnetsSuccess(): void
    {
        $response = self::makeRequest();
        self::assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        self::assertIsArray($data);

        foreach ($data as $subnet) {
            self::assertArrayHasKey('uuid', $subnet);
            self::assertArrayHasKey('ip', $subnet);
            self::assertArrayHasKey('mask', $subnet);
            self::assertArrayHasKey('floatgroupUuid', $subnet);
            self::assertArrayHasKey('owner', $subnet);
            self::assertIsString($subnet['uuid']);
            self::assertIsString($subnet['ip']);
            self::assertIsInt($subnet['mask']);
            self::assertIsString($subnet['floatgroupUuid']);
            self::assertIsString($subnet['owner']);
        }
    }

    public static function testGetAllSubnetsNotFound(): void
    {
        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_NOT_FOUND, ['error' => 'No subnets found.']),
        ]);
        self::initializeClient($mock);

        $response = self::makeRequest();
        self::assertEquals(HttpResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testGetAllSubnetsServerError(): void
    {
        $mock = new MockHandler([
            self::createMockResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, ['error' => 'Unexpected error occurred.']),
        ]);
        self::initializeClient($mock);

        $response = self::makeRequest();
        self::assertEquals(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // php bin/phpunit tests/Net/Ip4Subnet/Presentation/Rest/V1/E2EGetAllSubnetsTest.php
}




