<?php
declare(strict_types=1);

namespace Proxmox\Version\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Psr\Http\Message\ResponseInterface;
class E2EGetAllowedIp4ToUserTest extends WebTestCase
{

    protected static Client $client;
    protected static $token;
    protected static $userUuid = '5daf6e2d-9e6d-491b-aab5-0eb228620884';
    const ERROR = 'Error';
    const BEARER = 'Bearer';
    
    protected static $uri = '/api/v1/user/';
    protected static $resource = '/ip4s';

    public static function setUpBeforeClass(): void
    {
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




    }
    public static function getAllowedIp4sToUser(string $uri): ?ResponseInterface {
        try {
            return self::$client->get($uri, [
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
    public static function testGetIp4OK(): void
    {
        $response = self::getAllowedIp4sToUser(self::$uri.self::$userUuid.self::$resource);
        
        self::assertEquals(ResponseCode::HTTP_OK, $response->getStatusCode());
    }

    public function testGetIp4ToUserNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::getAllowedIp4sToUser(self::$uri.$faker->uuid().self::$resource);
        
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
    }
} // php bin/phpunit tests/User/Presentation/Rest/V1/E2EGetAllowedIp4ToUserTest.php