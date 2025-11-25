<?php
declare(strict_types=1);

namespace Node\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetNodeByUuidTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;


    public static function setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];

        self::login();

        self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    public static function testGetNodeByUuidOK(): void
    {
        $result = self::$client->get('/api/v1/node');
        $uuid= json_decode($result->getBody()->getContents(),true)[0]['uuid'];
        $response = self::$client->get('/api/v1/node/'. $uuid);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode( $response->getBody()->getContents(), true);

        self::assertIsArray($responseData);
        if (is_array($responseData) && count($responseData) > 0) {
            $nodeData = $responseData;
            self::assertArrayHasKey('uuid', $nodeData);
            self::assertArrayHasKey('gcp_node_name', $nodeData);
            self::assertArrayHasKey('pve_node_name', $nodeData);
            self::assertArrayHasKey('pve_hostname', $nodeData);
            self::assertArrayHasKey('cpu', $nodeData);
            /*  .....  */
            if (isset($nodeData['cpu']) && is_array($nodeData['cpu'])) {
                self::assertArrayHasKey('vendor', $nodeData['cpu']);
                self::assertArrayHasKey('name', $nodeData['cpu']);
                self::assertArrayHasKey('custom', $nodeData['cpu']);
            }
        } else {
            self::fail("No se recibieron datos válidos de la solicitud.");
        }
    }
    
    public static function testGetNodeByUuidNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::$client->request('GET','/api/v1/node/'. $faker->uuid(),['http_errors' => false]);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

}

// php bin/phpunit tests/Node/Presentation/Rest/V1/E2EGetNodeByUuidTest.php
