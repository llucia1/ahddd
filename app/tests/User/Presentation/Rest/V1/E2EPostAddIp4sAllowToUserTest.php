<?php
declare(strict_types=1);

namespace Proxmox\Version\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;


class E2EPostAddIp4sAllowToUserTest extends WebTestCase
{
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    protected static Client $client;
    protected static $token;
    protected static array $data;
    protected static string $url;
    protected static array $dataNotFound;
    protected static $uuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';

    protected static $uri = '/api/v1/user/';
    protected static $subUriIp4 = '/ip4s';
    public static function  setUpBeforeClass(): void
    {
        self::$data = [
            "ips" => [
                                "192.0.2.0",
                                "192.0.2.1"
                            ]
        ];

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);

        HandlerStack::create($mock);
        self::$client = new Client(['base_uri' => 'http://localhost:80']);


        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        self::$url = self::$uri . self::$uuid . self::$subUriIp4;
    }

    public static function postAddIp4sAllowToUser(string $uri, array $data): ?ResponseInterface {
        try {
            return self::$client->post($uri, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => self::BEARER . ' ' . self::$token
                ],
                'json' => $data
            ]);
        } catch (\Exception $e) {
            echo self::ERROR . ': ' . $e->getMessage();
            return null;
        }
    }

    public  static function testAddIp4sAllowToUserWhenAllDataIsNotCorrect():void{
        
        $response = self::postAddIp4sAllowToUser(self::$url, self::$data);
        
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public  static function testWhenUserNotFound():void{
        $faker = FakerFactory::create();
        
        $uri = self::$uri . $faker->uuid(). self::$subUriIp4;
        $response = self::postAddIp4sAllowToUser($uri, self::$data);

        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }
    // php bin/phpunit tests/User/Presentation/Rest/V1/E2EPostAddIp4sAllowToUserTest.php
}
