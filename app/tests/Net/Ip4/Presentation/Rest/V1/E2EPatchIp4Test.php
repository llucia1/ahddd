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





class E2EPatchIp4Test extends WebTestCase
{
    protected static Client $client;
    protected static $token;
    protected static array $correctRequestData;
    protected static array $notFoundRequestData;
    protected static array $parameterInorrectEquestData;

    protected static string $BEARER_TOKEN_PREFIX = 'Bearer ';
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

        $uuidNetwork = self::getNetwork();
        $ip = self::getIp();



        $faker = FakerFactory::create();
        self::$correctRequestData = [
            "ip" => $ip,
            "uuid_network" => $uuidNetwork
        ];

        self::$notFoundRequestData = [
            "ip" => $faker->ipv4(),
            "uuid_network" => $uuidNetwork
        ];

        self::$parameterInorrectEquestData = [
            "ip" => $faker->ipv4(),
            "uuid_network" => $faker->randomNumber(1),
        ];
    }

    public static function getNetwork(): mixed
    {
            $response = self::$client->get('/api/v1/ip4_network', [
                'headers' => [
                    'Authorization' => self::$BEARER_TOKEN_PREFIX . self::$token
                ]]);
            $uuids = json_decode($response->getBody()->getContents(), true);
            if (!empty($uuids)) {
                return $uuids[0]['uuid'];
            }
            return null;
    }

    public static function getIp(): mixed
    {
            $response = self::$client->get('/api/v1/ip4', [
                'headers' => [
                    'Authorization' =>  self::$BEARER_TOKEN_PREFIX .  self::$token
                ]]);
            $uuids = json_decode($response->getBody()->getContents(), true);
            if (!empty($uuids)) {
                return $uuids[0]['ip'];
            }
            return null;
    }
    public static function testEditIp4Success(): void
    {
            $response = self::$client->patch('/api/v1/ip4', [
                'headers' => [
                    'Authorization' =>  self::$BEARER_TOKEN_PREFIX .  self::$token
                ],
                'json' => self::$correctRequestData
            ]);
            
            self::assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public static function testEditIp4NotFound(): void
    {
        try {
            $response = self::$client->patch('/api/v1/ip4', [
                'headers' => [
                    'Authorization' =>  self::$BEARER_TOKEN_PREFIX .  self::$token
                ],
                'json' => self::$notFoundRequestData
            ]);
    
            self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    
            $responseData = json_decode($response->getBody()->getContents(), true);
    
            self::assertArrayHasKey('error', $responseData);
            self::assertStringContainsString(self::$notFoundRequestData['ip'], $responseData['error']);
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
            $responseData = json_decode($response->getBody()->getContents(), true);
            self::assertArrayHasKey('error', $responseData);
            self::assertStringContainsString(self::$notFoundRequestData['ip'], implode(', ', $responseData['error']));
        }
    }

    public static function testeditIp4Conflict(): void
    {

        $response = self::$client->request('PATCH', '/api/v1/ip4', ['http_errors' => false, 'json' => self::$correctRequestData]);
        self::assertEquals(ResponseCode::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
    public static function testeditIp4NotBodySend(): void
    {

        $response = self::$client->request('PATCH', '/api/v1/ip4', [
            'headers' => [
                'Authorization' =>  self::$BEARER_TOKEN_PREFIX .  self::$token
            ],
            'http_errors' => false
        ]);
        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testeditIp4DataIncorrect(): void
    {
        $response = self::$client->request('PATCH', '/api/v1/ip4', [
            'headers' => [
                'Authorization' =>  self::$BEARER_TOKEN_PREFIX .  self::$token
            ],
            'http_errors' => false,
            'json' => self::$parameterInorrectEquestData
        ]);
    
        self::assertEquals(ResponseCode::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

}// php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2EPatchIp4Test.php