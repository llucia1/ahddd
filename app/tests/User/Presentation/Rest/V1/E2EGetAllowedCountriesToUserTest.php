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
class E2EGetAllowedCountriesToUserTest extends WebTestCase
{
//              ESTE CODIGO NO ME LO VALIDA SONAR. QUE VENGA DIOS Y LO VEA
//              NO ES LA PRIMERA VEZ QUE ME PASA ESTO Y PORQUE ESTE EN VERDE PIERDO UNA BARBARIDAD DE TIEMPÒ



    protected static Client $client;
    protected static string $token;
    protected static string $userUuid = '5daf6e2d-9e6d-491b-aab5-0eb228620884';
    private const ERROR = 'Error';
    private const BEARER = 'Bearer';
    private const BASE_URI = 'http://localhost:80';
    private const AUTH_ENDPOINT = '/api/v1/auth/login';
    private const USER_ENDPOINT = '/api/v1/user/';
    private const RESOURCE = '/counties';

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler([
            new Response(ResponseCode::HTTP_OK, [], json_encode(['token' => 'fake_token'])),
            new Response(ResponseCode::HTTP_OK),
            new Response(ResponseCode::HTTP_CONFLICT)
        ]);

        $stack = HandlerStack::create($mock);

        self::$client = new Client(['base_uri' => self::BASE_URI, 'handler' => $stack]);

        $response = self::$client->post(self::AUTH_ENDPOINT, [
            'json' => [
                'email' => 'xavi@xavi.com',
                'password' => 'password'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        self::$token = $data['token'];
    }

    private static function makeGetRequest(string $uri): ?ResponseInterface
    {
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

    private function assertResponseCode(string $uuid, int $expectedCode): void
    {
        $uri = self::USER_ENDPOINT . $uuid . self::RESOURCE;
        $response = self::makeGetRequest($uri);
        self::assertEquals($expectedCode, $response->getStatusCode());
    }

    /**
     * @dataProvider provideCountriesData
     */
    public function testGetCountriesToUser(string $uuid, int $expectedCode): void
    {
        $this->assertResponseCode($uuid, $expectedCode);
    }

    public function provideCountriesData(): array
    {
        $faker = FakerFactory::create();
        return [
            'existing_user' => [self::$userUuid, ResponseCode::HTTP_OK],
            'non_existing_user' => [$faker->uuid(), ResponseCode::HTTP_CONFLICT],
        ];
    }
}// php bin/phpunit tests/User/Presentation/Rest/V1/E2EGetAllowedCountriesToUserTest.php