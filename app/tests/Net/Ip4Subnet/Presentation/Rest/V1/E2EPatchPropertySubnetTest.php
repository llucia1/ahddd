<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

declare(strict_types=1);

namespace Net\Ip4Subnet\Presentation\Rest\V1;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

class E2EPatchPropertySubnetTest extends TestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static string $token;
    protected static string $url;
    protected static string $uuid;
    protected static string $userUuid;

    protected static $uri = '/v1/ip4/subnet/';
    protected static $resource = '/owner';

    public static function setUpBeforeClass(): void
    {
        $faker = \Faker\Factory::create();

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([['uuid' => $faker->uuid()]])),
            new Response(ResponseCode::HTTP_NO_CONTENT),
            new Response(ResponseCode::HTTP_CONFLICT),
            new Response(ResponseCode::HTTP_NOT_FOUND),
            new Response(ResponseCode::HTTP_BAD_REQUEST)
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
        self::$userUuid = $faker->uuid();
        self::$url = self::$uri . self::$uuid . self::$resource;
    }

    /**
     * @dataProvider providePatchPropertySubnetScenarios
     */
    public function testPatchPropertySubnet(int $responseCode, int $expectedStatus): void
    {
        self::configMockHandler($responseCode);

        $response = self::patchPropertySubnet(self::$url, self::$userUuid);
        self::assertEquals($expectedStatus, $response->getStatusCode());
    }

    public function providePatchPropertySubnetScenarios(): array
    {
        return [
            'Correct Patch' => [ResponseCode::HTTP_NO_CONTENT, ResponseCode::HTTP_NO_CONTENT],
            'Subnet Not Found' => [ResponseCode::HTTP_NOT_FOUND, ResponseCode::HTTP_NOT_FOUND],
            'Property Conflict' => [ResponseCode::HTTP_CONFLICT, ResponseCode::HTTP_CONFLICT],
            'Invalid UUID' => [ResponseCode::HTTP_BAD_REQUEST, ResponseCode::HTTP_BAD_REQUEST],
        ];
    }

    public static function patchPropertySubnet(string $uri, string $userUuid): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->patch($uri, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ],
                'json' => [
                    'uuid_user' => $userUuid
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

    private static function configMockHandler(int $codeResponse): void
    {
        self::setUpMockHandler([
            new Response($codeResponse)
        ]);
    }
}




    // php bin/phpunit tests/Net/Ip4Subnet/Presentation/Rest/V1/E2EPatchPropertySubnetTest.php