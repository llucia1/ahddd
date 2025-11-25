<?php
declare(strict_types=1);

namespace Net\Ip4Subnet\Presentation\Rest\V1;

use Symfony\Component\HttpFoundation\Response as ResponseCode;
use GuzzleHttp\Handler\MockHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Psr7\Response;
use Tests\Net\Ip4Subnet\Application\Helper\MockerTrait;
use Faker\Factory as FakerFactory;
use Faker\Generator;
class E2EDeleteSubnetTest extends WebTestCase
{
use MockerTrait;
    
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static string $token;
    protected static string $url;
    protected static string $uuid;
    protected static $uri = '/v1/ip4/subnet/';
    protected static $faker;
    public static function setUpBeforeClass(): void
    {
        self::$faker =  FakerFactory::create();
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK, [], json_encode([['uuid' => self::$faker->uuid()]])),
            new Response(ResponseCode::HTTP_NO_CONTENT),
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

        self::$uuid = self::$faker->uuid();
        self::$url = self::$uri . self::$uuid;
    }

    public static function deleteSubnet(string $uri): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return self::$client->delete($uri, [
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

    public static function testDeleteSubnetIsCorrect(): void
    {
        $code = ResponseCode::HTTP_NO_CONTENT;
        self::configMockHandler ($code);
        self::exeDeleteSubnet(self::$url, $code);
    }

    public static function testDeleteSubnetNotFound(): void
    {
        $code = ResponseCode::HTTP_NOT_FOUND;
        self::configMockHandler($code);
        self::exeDeleteSubnet(self::$url, $code);
    }

    public static function testDeleteSubnetInvalidUuid(): void
    {
        $code = ResponseCode::HTTP_BAD_REQUEST;

        self::configMockHandler($code);
        $invalidUrl = self::$uri . self::$faker->name;
        self::exeDeleteSubnet($invalidUrl, $code);
    }

    private static function exeDeleteSubnet(string $url, int $code): void
    {
        $response = self::deleteSubnet($url);
        self::assertEquals($code, $response->getStatusCode());
    }
    
}

    // php bin/phpunit tests/Net/Ip4Subnet/Presentation/Rest/V1/E2EDeleteSubnetTest.php
