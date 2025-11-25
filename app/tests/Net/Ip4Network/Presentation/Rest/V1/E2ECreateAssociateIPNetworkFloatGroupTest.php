<?php
declare(strict_types=1);

namespace Net\Ip4Network\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use SebastianBergmann\Type\MixedType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class E2ECreateAssociateIPNetworkFloatGroupTest extends WebTestCase
{
    protected static $client;
    protected static array $userLogin;
    protected static array $floatGroups;
    protected static string $networkId;
    protected static array $correctRequestData;
    protected static array $requestDataFloatGroupNotActive;
    protected static array $parameterIncorrectRequestData;

    protected static string $jwt;
    protected static string $uri = '/api/v1/ip4_network/';
    protected static string $resource = '/add_float_group';

    protected static array $httpErrors = ['http_errors' => false];

    public static function setUpBeforeClass(): void
    {

        $faker = FakerFactory::create();



        self::$userLogin = [
            "email" => "xavi@xavi.com",
            "password" => "password"
        ];


        self::$correctRequestData = [
            "uuid" => $faker->uuid(),
        ];

        self::$requestDataFloatGroupNotActive = [
            "uuid" => '35b0e1e2-1d82-454e-8d71-a43eeb027443',
        ];
        self::$parameterIncorrectRequestData = [
            "uuid" => $faker->uuid(),
        ];

       self::$networkId = '8';

       self::login();
       self::$client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.self::$jwt]]);


       self::$floatGroups = self::getFloatGroups();


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

    private function getNetworks():mixed
    {
        $response = self::$client->get('/api/v1/ip4_network');
        return json_decode($response->getBody()->getContents(), true);

    }

    private function runPost( string $uri, array $data): mixed
    {
        return self::$client->post($uri, $data);
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
        $result = $client->post('/api/v1/auth/login',['json'=>self::$userLogin]);
        self::$jwt =   json_decode($result->getBody()->getContents())->token;
    }
    
    public function testAssociateFloatGroupToIp4NetworkSuccess(): void
    {
        $float = $this->getOneFloatGroupActive();
        $networks= $this->getNetworks();
        self::$correctRequestData['uuid'] = $float['uuid'];
        $network=$networks[0]['uuid'];
        $response = $this->runPost(self::$uri . $network . self::$resource, ['json' => self::$correctRequestData]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
    public function getOptionsData(array $json, bool $httpErrors = false): array
    {
        return ['http_errors' => $httpErrors, 'json' => $json];
    }
    public function testAssociateFloatGroupNotExist(): void
    {
        self::$correctRequestData['uuid'] = 'uuidNotExists';

        $response = $this->runPost(self::$uri . self::$networkId . self::$resource, $this->getOptionsData(self::$correctRequestData));
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    public function testAssociateFloatGroupNotActive(): void
    {

        $response = $this->runPost(self::$uri . self::$networkId . self::$resource, $this->getOptionsData(self::$requestDataFloatGroupNotActive));
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    public function testAssociate4NetworkNotActive(): void
    {
        $networkId = 3;
        $response = $this->runPost(self::$uri .'-'. $networkId . self::$resource, $this->getOptionsData(self::$correctRequestData));
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    public function testAssociate4NetworkNotExists(): void
    {

        $response = $this->runPost(self::$uri .'-'. self::$networkId . self::$resource, $this->getOptionsData(self::$correctRequestData));
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}

/*
php bin/phpunit tests/Net/Ip4Network/Presentation/Rest/V1/E2ECreateAssociateIPNetworkFloatGroupTest.php
    */