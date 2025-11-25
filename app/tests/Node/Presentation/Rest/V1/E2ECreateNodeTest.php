<?php
declare(strict_types=1);

namespace Node\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2ECreateNodeTest extends WebTestCase
{
    protected static array $data;
    protected static $client;
    protected static string $JWT;
    protected static array $USER_LOGIN;


    public static function  setUpBeforeClass(): void
    {
        $faker = FakerFactory::create();

        self::$USER_LOGIN = [
            "email" => "admin@admin.com",
            "password" => "password",
        ];
        self::$data = [

            "gcp_node_name" => $faker->name(),
            "pve_node_name" => $faker->name(),
            "pve_hostname" => $faker->name(),
            "pve_username" => $faker->userName(),
            "pve_password" => $faker->password(),
            "os" => "Debian 12",
            "pve_realm" => "pam",
            "pve_port"=> 8006,
            "pve_ip" => $faker->ipv4(),
            "ssh_port" => $faker->randomNumber(4)
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


    public function testCreateNodeWhenAllDataIsCorrect():void{
        $response1 = $this->createNode();
        self::assertEquals(Response::HTTP_CREATED, $response1->getStatusCode());
    }

    public  static function testCreateNodeWhenNotBodySend():void{
        $response = self::$client->request('POST', '/api/v1/node',['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateNodeWhenNameExist():void{
        $response = self::$client->request('POST', '/api/v1/node',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function createNode()
    {
        return self::$client->post('/api/v1/node', [
            'json' => self::$data,
            'http_errors' => false
        ]);
    }

    public function testPotsNodeWhenDuplicate(): void
    {

      

        $response2 = $this->createNode();

        self::assertEquals(Response::HTTP_CONFLICT, $response2->getStatusCode());
    }


}
// php bin/phpunit tests/Node/Presentation/Rest/V1/E2ECreateNodeTest.php