<?php
declare(strict_types=1);

namespace Net\Ip4\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;





class E2ECreateIp4Test extends WebTestCase
{
    protected static Client $client;
    protected static $token;
    protected static array $CORRECT_REQUEST_DATA;
    protected static array $INCORRECT_REQUEST_DATA;
    protected static array $PARAMETER_INCORRECT_REQUEST_DATA;

    public static function setUpBeforeClass(): void
    {

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
            new Response(ResponseCode::HTTP_NO_CONTENT)
        ]);



        $handlerStack = HandlerStack::create($mock);
        
        self::$client = new Client(['base_uri' => 'http://localhost:80']);


        $response = self::$client->post('/api/v1/auth/login', [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        $uuidNetwork = self::getNetwork();



        $faker = FakerFactory::create();
        self::$CORRECT_REQUEST_DATA = [
            "ip" => $faker->ipv4(),
            "uuid_network" => $uuidNetwork
        ];

        self::$INCORRECT_REQUEST_DATA = [
            "uuid_network" => 'd0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0',
        ];

        self::$PARAMETER_INCORRECT_REQUEST_DATA = [
            "ip" => $faker->ipv4(),
            "uuid_network" => $faker->randomNumber(1),
        ];
    }

    public static function getNetwork(): mixed
    {
            $response = self::$client->get('/api/v1/ip4_network', [
                'headers' => [
                    'Authorization' => 'Bearer ' . self::$token
                ]]);
            $uuids = json_decode($response->getBody()->getContents(), true);
            if (!empty($uuids)) {
                return $uuids[0]['uuid'];
            }
            return null;
    }

    public static function testCreateIp4Success(): void
    {

        $response = self::$client->post('/api/v1/ip4', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            
            'json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(ResponseCode::HTTP_CREATED, $response->getStatusCode());
    }

    public static function testCreateIp4Conflict(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4', ['http_errors' => false, 'json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(ResponseCode::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
    public static function testCreateIp4BadRequest(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4', ['http_errors' => false, 'json' => self::$INCORRECT_REQUEST_DATA]);
        self::assertEquals(ResponseCode::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public static function testCreateIp4NotBodySend(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4', ['http_errors' => false]);
        self::assertEquals(ResponseCode::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public static function testCreateIp4DataIncorrect(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4', ['http_errors' => false, ['body' => self::$PARAMETER_INCORRECT_REQUEST_DATA]]);
        self::assertEquals(ResponseCode::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

}// php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2ECreateIp4Test.php