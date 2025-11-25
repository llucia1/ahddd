<?php
declare(strict_types=1);

namespace Net\Ip4\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class E2EDeletePropertySubnetTest extends WebTestCase// NOSONAR
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static string $token;
    protected static string $url;
    protected static string $uuid;

    protected static $uri = '/v1/ip4/subnet/';
    protected static $resource = '/owner';

    public static function setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([['uuid' => $faker->uuid()]])),
        ]);

        self::setUpClient($mock);

        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$uuid = $faker->uuid();
        self::$url = self::$uri . self::$uuid . self::$resource;
    }

    public static function deletePropertySubnet(string $uri): ?\Psr\Http\Message\ResponseInterface
    {
        return self::sendRequest('delete', $uri);
    }

    private static function sendRequest(string $method, string $uri): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->request($method, $uri, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ]
            ]);
        } catch (\Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }

    private static function setUpMockHandler(array $responses): void
    {
        $mock = new MockHandler($responses);
        self::setUpClient($mock);
    }

    private static function setUpClient(MockHandler $mock): void
    {
        $handler = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handler, 'base_uri' => 'http://localhost:80']);
    }

    public static function testDeletePropertySubnetIsCorrect(): void
    {
        self::configMockHandler(ResponseCode::HTTP_NO_CONTENT);

        $response = self::deletePropertySubnet(self::$url);
        self::assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public static function testDeletePropertySubnetNotFound(): void
    {
        self::configMockHandler(ResponseCode::HTTP_NOT_FOUND);

        $response = self::deletePropertySubnet(self::$url);
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function testDeletePropertySubnetConflict(): void
    {
        self::configMockHandler(ResponseCode::HTTP_CONFLICT);

        $response = self::deletePropertySubnet(self::$url);
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
    }

    public static function testDeletePropertySubnetInvalidUuid(): void
    {
        self::configMockHandler(ResponseCode::HTTP_BAD_REQUEST);

        $faker = FakerFactory::create();
        $invalidUrl = self::$uri . $faker->word . self::$resource;

        $response = self::deletePropertySubnet($invalidUrl);
        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private static function configMockHandler(int $codeResponse): void
    {
        self::setUpMockHandler([
            new Response($codeResponse)
        ]);
    }
}
// php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2EDeletePropertySubnetTest.php
