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


class E2EPostAddCoutriesAllowToUserTest extends WebTestCase
{
    protected static Client $client;
    protected static $token;
    protected static array $data;
    protected static array $dataNotFound;
    protected static $uuid = 'ae901ebb-656f-44ff-b7d4-80bae65629c2';
    public static function  setUpBeforeClass(): void
    {
        self::$data = [
            "countries" => [
                                "ES"
                            ]
        ];
        self::$dataNotFound = [
            "countries" => [
                                "XX"
                            ]
        ];

        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_CREATED, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_NOT_FOUND)
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

    public static function postAddCountriesAllowToUser(array $data): ?ResponseInterface {
        try {
            return self::$client->post('/api/v1/user/' . self::$uuid . '/countries', [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . self::$token
                ],
                'json' => $data
            ]);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public  static function testAddCountriesAllowToUserWhenAllDataIsCorrect():void{
        
        $response = self::postAddCountriesAllowToUser(self::$data);
        
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public  static function testAddCountriesAllowToUserWhenAllDataIsNotCorrect():void{
        
        $response = self::postAddCountriesAllowToUser(self::$dataNotFound);
        
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public  static function testWhenUserNotFound():void{
        $faker = FakerFactory::create();
        
        try {
            $response = self::$client->post('/api/v1/user/' . $faker->uuid(). '/countries', [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . self::$token
                ],
                'json' => self::$data
            ]);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        
        self::assertEquals(ResponseCode::HTTP_NOT_FOUND, $response->getStatusCode());
    }
    // php bin/phpunit tests/User/Presentation/Rest/V1/E2EPostAddCoutriesAllowToUserTest.php
}
