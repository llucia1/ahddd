<?php
declare(strict_types=1);

namespace Net\Ip4Network\Presentation\Rest\V1;

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class E2ECreateIP4NetworkTest extends WebTestCase
{
    protected static $client;
    protected static array $USER_LOGIN;
    protected static array $CORRECT_REQUEST_DATA;
    protected static array $INCORRECT_REQUEST_DATA;
    protected static array $PARAMETER_INCORRECT_REQUEST_DATA;

    protected static string $JWT;

    public static function setUpBeforeClass(): void
    {

        $faker = FakerFactory::create();



        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];


        self::$CORRECT_REQUEST_DATA = [
            "name" => $faker->name(),
            "name_server1" => $faker->ipv4(),
            "name_server2" => $faker->ipv4(),
            "name_server3" => $faker->ipv4(),
            "name_server4" => $faker->ipv4(),
            "priority" => $faker->randomNumber(1),
            "netmask" => $faker->ipv4(),
            "float_group" => 1,
            "gateway" => $faker->ipv4(),
            "broadcast" => $faker->ipv4(),
        ];

        self::$INCORRECT_REQUEST_DATA = [
            "name_server1" => $faker->ipv4(),
            "name_server2" => $faker->ipv4(),
            "name_server3" => $faker->ipv4(),
            "name_server4" => $faker->ipv4(),
            "priority" => $faker->randomNumber(1),
            "netmask" => $faker->ipv4(),
            "gateway" => $faker->ipv4(),
            "broadcast" => $faker->ipv4(),
        ];
        self::$PARAMETER_INCORRECT_REQUEST_DATA = [
            "name" => $faker->name(),
            "name_server1" => $faker->ipv4(),
            "name_server2" => $faker->ipv4(),
            "name_server3" => $faker->ipv4(),
            "name_server4" => $faker->ipv4(),
            "priority" => "1",
            "netmask" => $faker->ipv4(),
            "gateway" => $faker->ipv4(),
            "broadcast" => $faker->ipv4(),
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
    /*
    $uuid = $this->createIPNetwork->__invoke($ip4Network);
    return new JsonResponse(['uuid' => $uuid], Response::HTTP_CREATED);

    


}catch(ErrorFloatGroupOrNetworkNotExist|ErrorFloatGroupNotExist $e) {
    $this->logger->error("Error in create IP4_NETOWRK" . $request->getName() . " :( ->" . $e->getMessage());
    return new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
} catch(IP4NetworkDuplicated $e){
    $this->logger->error("Error in create IP4_NETOWRK".$request->getName()." :( ->". $e->getMessage());
    return  new JsonResponse(["error"=>$e->getMessage()], Response::HTTP_CONFLICT);
}catch(Error $e){
    $this->logger->error("Error in create IP4_NETOWRK".$request->getName()." :( ->". $e->getMessage());
    return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

}
*/
    public static function testCreateIpNetworkSuccess(): void
    {

        $response = self::$client->post('/api/v1/ip4_network',  ['json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public static function testCreateIpNetworkNameNetworkConflict(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4_network', ['http_errors' => false, 'json' => self::$CORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }
    public static function testCreateIpNetworkBadRequest(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4_network', ['http_errors' => false, 'json' => self::$INCORRECT_REQUEST_DATA]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIpNetworkNotBodySend(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4_network', ['http_errors' => false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateIpNetworkDataIncorrect(): void
    {

        $response = self::$client->request('POST', '/api/v1/ip4_network', ['http_errors' => false, ['body' => self::$PARAMETER_INCORRECT_REQUEST_DATA]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

}