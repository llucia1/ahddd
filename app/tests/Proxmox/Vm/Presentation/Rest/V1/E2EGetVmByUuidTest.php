<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2EGetVmByUuidTest extends WebTestCase
{
    protected static Client $client;
    protected static string $JWT;
    protected static string $uuid;
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
 
    public static function testGetVmByUuidOK(): void
    {

        $uuid = '19cd5c15-446e-411c-bf6f-1e493528cdfb';
        $response = self::$client->get('/api/v1/pve/vm/'. $uuid);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode( $response->getBody()->getContents(), true);

        self::assertIsArray($responseData);
        if (is_array($responseData) && count($responseData) > 0) {
            $nodeData = $responseData;
            self::assertArrayHasKey('uuid', $nodeData);
            self::assertArrayHasKey('name', $nodeData);
            self::assertArrayHasKey('node', $nodeData);
            self::assertArrayHasKey('cores', $nodeData);
            self::assertArrayHasKey('cpu', $nodeData);
            self::assertArrayHasKey('os', $nodeData);
            self::assertArrayHasKey('active', $nodeData);
            //  .....  
            if (isset($nodeData['node']) && is_array($nodeData['node'])) {
                self::assertIsArray($nodeData['node']);
                self::assertArrayHasKey('pve_hostname', $nodeData['node']);
                self::assertArrayHasKey('pve_username', $nodeData['node']);
                self::assertArrayHasKey('pve_realm', $nodeData['node']);
                // ....... 
                if (isset($nodeData['node']['cpu']) && is_array($nodeData['node']['cpu'])) {
                    self::assertIsArray($nodeData['node']['cpu']);
                    self::assertArrayHasKey('vendor', $nodeData['node']['cpu']);
                    self::assertArrayHasKey('custom', $nodeData['node']['cpu']);
                    self::assertArrayHasKey('name', $nodeData['node']['cpu']);
                }
            }
        } else {
            self::fail("No se recibieron datos válidos de la solicitud.");
        }
        
    }
    
    public static function testGetVmByUuidNotExist(): void
    {
        $faker = FakerFactory::create();
        $response = self::$client->request('GET','/api/v1/pve/vm/'. $faker->uuid(),['http_errors' => false]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
// php bin/phpunit tests/Proxmox/Vm/Presentation/Rest/V1/E2EGetVmByUuidTest.php
}