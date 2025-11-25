<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Presentation\Rest\V1;


use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class E2EPacthFloatGroupTest extends WebTestCase
{
    protected static $client;
    protected static string $UUID = "29c63104-10a6-435d-a657-c2b585d345ef";
    protected static array $USER_LOGIN;
    protected static array $FLOAT_GROUPS;
    protected static array $CORRECT_REQUEST_DATA1;
    protected static array $CORRECT_REQUEST_DATA2;
    protected static array $CORRECT_REQUEST_DATA3;
    protected static array $REQUEST_DATA_FLOATGROUP_NOTACTIVE;
    protected static array $PARAMETER_INCORRECT_REQUEST_DATA;

    protected static string $JWT;

    public static function setUpBeforeClass(): void
    {

        $faker = FakerFactory::create();



        self::$USER_LOGIN = [
            "email" => "user2@email.es",
            "password" => "user2"
        ];


        self::$CORRECT_REQUEST_DATA1 = [
            "name"=> "NameTest_1",
        ];
        self::$CORRECT_REQUEST_DATA2 = [
            "name"=> "NameTest_1",
            "active" => true,
        ];
        self::$CORRECT_REQUEST_DATA3 = [
            "name"=> "NameTest_1",
            "active" => false,
        ];

        self::$REQUEST_DATA_FLOATGROUP_NOTACTIVE = [
            "name" => 'Barcelona',
        ];
        self::$PARAMETER_INCORRECT_REQUEST_DATA = [
            "name" => "Madri?1$=d",
        ];


       self::login();
       self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$JWT]]);


    }
    private static function getFloatGroups(): array
    {
        $response = self::$client->get('/api/v1/float_group');
        return json_decode($response->getBody()->getContents(), true);
    }

    private function getFloatGroupByUuid($uuid): mixed
    {
        $response = self::$client->get('/api/v1/float_group/'.$uuid);
        return json_decode($response->getBody()->getContents(), true);
    }

    private function getOneFloatGroupNotActive(): mixed
    {

        $floats = self::getFloatGroups();
            $r = null;
            foreach ( $floats as $float ) {
                $noActive = $this->getFloatGroupByUuid($float['uuid']);

                if ($noActive['active'] === false) {
                    $r = $noActive;
                }
            }
            return $r;
    }

    private function getOneFloatGroupActive(): mixed
    {

        $floats = self::getFloatGroups();

            foreach ( $floats as $float ) {
                $active = $this->getFloatGroupByUuid($float['uuid']);

                if ($active['active']) {
                    return $active;
                }
            }

            return null;
    }

    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }
    
    public function testUpdateFloatGroupSuccess(): void
    {
        $floatGroup = $this->getOneFloatGroupActive();

        $response = self::$client->patch('/api/v1/float_group/' . $floatGroup['uuid'], ['json' => self::$CORRECT_REQUEST_DATA1]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }    


    public function testUpdateFloatGroupByUuidNotExists(): void
    {
        $faker = FakerFactory::create();
    
        try {
            $response = self::$client->patch('/api/v1/float_group/' . $faker->uuid(), ['json' => self::$CORRECT_REQUEST_DATA1]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
    
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUpdateFloatGroupNotExistsIsNoActive(): void
    {
        // AQUI NO ME QUEDA OTRA OPCION QUE BUSCAR EN LA BD UN FLOATGROUP QUE NO ESTE ACTIVO
        $floatGroupNoactive = [
            'uuid' => '35b0e1e2-1d82-454e-8d71-a43eeb027443'
        ];
        try {
            $response = self::$client->patch('/api/v1/float_group/' . $floatGroupNoactive['uuid'], ['json' => self::$CORRECT_REQUEST_DATA1]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
    
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}


