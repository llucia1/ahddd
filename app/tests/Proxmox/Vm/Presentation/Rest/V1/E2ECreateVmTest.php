<?php
declare(strict_types=1);

namespace Proxmox\Vm\Presentation\Rest\V1;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class E2ECreateVmTest extends WebTestCase
{
    protected static array $data;
    protected static Client $client;
    protected static array $USER_LOGIN;
    protected static string $JWT;

    public static function  setUpBeforeClass(): void
    {
        self::$USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];


        self::$data = [
            "proxmox_node_name"=>"ns1000",
            "proxmox_cpu_name"=>"GenuineIntel",
            "proxmox_node"=> "ns1000",
            "proxmox_vmid"=> 102,
            "proxmox_cores"=> 2,
            "proxmox_name"=> "Prueba",
            "proxmox_netId"=> 0,
            "proxmox_netModel"=> "virtio",
            "proxmox_netBridge"=> "vmbr0",
            "proxmox_netFirewal"=> 1,
            "proxmox_OnBoot"=> true,
            "proxmox_scsihw"=> "virtio-scsi-pci",
            "proxmox_scsiId"=> 0,
            "proxmox_main"=> 0,
            "proxmox_discard"=> "on",
            "proxmox_cache"=> "directsync",
            "proxmox_importFrom"=> "/image/images/000/Debian-12-x86_64-GridCP-PVE_KVM-20231012.qcow2",
            "proxmox_tags"=> "deb12",
            "proxmox_ideId"=> 0,
            "proxmox_ideFile"=> "main:cloudinit",
            "proxmox_boot"=> "c",
            "proxmox_bootDisk"=> "scsi0",
            "proxmox_agent"=> "1",
            "proxmox_ipIndex"=> 0,
            "proxmox_ip"=> "5.134.113.50/24",
            "proxmox_gateway"=> "5.134.113.1",
            "proxmox_userName"=> "root",
            "proxmox_password"=> "password",
            "proxmox_cpuTypes"=> "x86-64-v2-AES",
            "proxmox_memory"=> 4096,
            "proxmox_ballon"=> 0
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

    public static function testCreateVmSuccess(): void
    {

        $response = self::$client->post('/api/v1/proxmox/vm', ['json' => self::$data]);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public  static function testCreateVmWhenNotBodySend():void{
        $response = self::$client->request('POST', '/api/v1/proxmox/vm',['http_errors'=>false]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateVmWhenNodeNameExist():void{
        $response = self::$client->request('POST', '/api/v1/proxmox/vm',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public static function testCreateVmWhenCpuTypeExist():void{
        $response = self::$client->request('POST', '/api/v1/proxmox/vm',['http_errors'=>false,['body'=>self::$data]]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }


}