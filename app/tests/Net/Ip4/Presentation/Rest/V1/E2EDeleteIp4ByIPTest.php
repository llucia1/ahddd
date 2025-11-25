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

class E2EDeleteIp4ByIPTest extends WebTestCase// NOSONAR
{
    protected static Client $client;// NOSONAR
    protected static $token;// NOSONAR
    protected static $uuidNetwork;// NOSONAR
    protected static array $CREATE_REQUEST_DATA;// NOSONAR
    protected static array $CORRECT_REQUEST_DATA;// NOSONAR

    public static function setUpBeforeClass(): void// NOSONAR
    {
        $faker = FakerFactory::create();
        $ip1 = $faker->ipv4();
        $ip1 = '1.1.0.100';
        self::$CORRECT_REQUEST_DATA = [
            "ips" => [$ip1]
        ];
        
        self::$CREATE_REQUEST_DATA = [
            "ip" => $ip1,
            "uuid_network" => '',
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
                'email' => 'admin@admin.com',
                'password' => 'password'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];

        
    }

    public function testDeleteIp4ByIpNotExist(): void
    {
        $datas = ['ips' => ['203.0.113.99']];
        
        $response = self::$client->request('DELETE', '/api/v1/ip4', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            'json' => $datas
        ]);
        
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteIP4ByIpOK(): void// NOSONAR
    {
            $this->getIp4Network();
            $this->createIp4();
            $this->assertDeleteIp4ByIp();
    }
    private function getIp4Network(): void// NOSONAR
    {

        $response = self::$client->get('/api/v1/ip4_network', ['headers' => [// NOSONAR
                                                                        'Authorization' => 'Bearer ' . self::$token// NOSONAR
                                                                    ]]);
        $uuids = json_decode($response->getBody()->getContents(), true);

        self::$uuidNetwork = $uuids[0]['uuid'];
        self::$CREATE_REQUEST_DATA["uuid_network"] = self::$uuidNetwork;
    }
    private function createIp4(): void// NOSONAR
    {
        self::$client->post('/api/v1/ip4', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            
            'json' => self::$CREATE_REQUEST_DATA
        ]);
    }

    private function assertDeleteIp4ByIp(): void// NOSONAR
    {
        $response = self::$client->delete('/api/v1/ip4', [// NOSONAR
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token// NOSONAR
            ],
            'json'=> self::$CORRECT_REQUEST_DATA
        ]);
        self::assertEquals(ResponseCode::HTTP_NO_CONTENT, $response->getStatusCode());// NOSONAR
    }


    public function testDeleteIp4ThrowsIp4GenuineNotValidDelete(): void
    {
        $faker = FakerFactory::create();
        $fakeIp = '5.134.113.50';
    
        $response = self::$client->delete('/api/v1/ip4', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            'json' => ['ips' => [$fakeIp]]
        ]);
    
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
        $body = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('error', $body);
    }
    
    public function testDeleteIp4ThrowsIp4InSubnetNotValidDelete(): void
    {
        $faker = FakerFactory::create();
        $fakeIp = '5.134.113.0';
        $response = self::$client->delete('/api/v1/ip4', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . self::$token
            ],
            'json'=> ['ips' => [$fakeIp]]
        ]);
    
        self::assertEquals(ResponseCode::HTTP_CONFLICT, $response->getStatusCode());
        $body = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('error', $body);
    }



}
// php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2EDeleteIp4ByIPTest.php
