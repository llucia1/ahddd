<?php
declare(strict_types=1);

namespace Tests\Proxmox\Vm\Application\Service; // Mismo namespace que el test

use Faker\Factory as FakerFactory;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use GridCP\Proxmox\Vm\Domain\VO\Vm;
use DateTime;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

use GridCP\Proxmox_Client\Nodes\Domain\Responses\NodesResponse as NodesResponseApiProxmox;
use GridCP\Proxmox_Client\Nodes\Domain\Responses\NodeResponse as NodeResponseApiPROXMOX;
use GridCP\Node\Application\Response\NodeResponse;

use GridCP\Proxmox_Client\Cpus\Domain\Reponses\CpuResponse;
use GridCP\Proxmox\Cpu\Application\Response\CpuResponses;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use function Lambdish\Phunctional\repeat;

trait VmHelper
{
    private function createVmEntity(?string $uuidVm, ?Vm $vmVo = null): VmEntity// NOSONAR
    {
        $faker = FakerFactory::create();

        $vmEntity = new VmEntity();
        $vmEntity->setId($faker->numberBetween(1, 100));
        $vmEntity->setUuid($uuidVm);

        return $vmEntity;
    }

    private function createIp4Entity(?string $ip, ?Vm $vmVo = null): Ip4Entity// NOSONAR
    {
        $faker = FakerFactory::create();

        $vmEntity = new Ip4Entity();
        $vmEntity->setUuid($faker->uuid());
        $vmEntity->setIp($ip);

        $reflection = new \ReflectionClass($vmEntity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);// NOSONAR
        $property->setValue($vmEntity, $faker->numberBetween(1, 100));// NOSONAR

        return $vmEntity;
    }

    private function nodesResponseApiPROXMOX(string $pce_name): NodesResponseApiPROXMOX
    {
    
        $nodeResponse = new NodeResponseApiProxmox(
             'online',
             '',
             'node/' . $pce_name,
             'EE:6D:63:D0:DA:A8:86:BF:7E:08:04:33:CB:FE:21:1D:49:37:94:33:C4:D7:DF:32:C3:1C:D6:7F:DD:97:F1:04',
             134411403264,
             7902818304,
             1457975,
             1728245760,
             $pce_name,
             0.0013576056772601,
             16,
             'node',
             36403986432
        );
    
        return new NodesResponseApiProxmox($nodeResponse);
    }


    private function nodeResponse( Vm $vmVo, string $pveName ): NodeResponse
    {
        $faker = FakerFactory::create();
        return new NodeResponse(
            $faker->uuid,
            $vmVo->gcpNode()->value(),
            $pveName,
            $faker->ipv4,
            'user',
            'password',
            'realm',
            8006,
            $vmVo->netIp()->value(),
            22,
            'es',
            'es-es',
            'es',
            'nvme',
            'nvme-iso',
            'nvme-image',
            'nvme-backup',
            '',
            [],
            1,
            8,
            []
        );
    }


    private function cpuResponse(string $cpuYaml):CpuResponses {
        return new CpuResponses([
            new CpuResponse('GenuineIntel', 'SandyBridge', 0),
            new CpuResponse($cpuYaml, $cpuYaml, 0),
            new CpuResponse('default', 'max', 0),
            new CpuResponse('AuthenticAMD', 'EPYC-IBPB', 0),
            new CpuResponse('GenuineIntel', 'SandyBridge-IBRS', 0),
            new CpuResponse('AuthenticAMD', 'EPYC-Rome-v2', 0),
            new CpuResponse('GenuineIntel', 'pentium3', 0),
            new CpuResponse('GenuineIntel', 'SapphireRapids-v2', 0),
            new CpuResponse('GenuineIntel', 'Haswell-noTSX-IBRS', 0),
            new CpuResponse('GenuineIntel', 'Skylake-Server', 0)
        ]);
    }


    private function subnetEntity( Vm $vmVo, string $floatgroupuuid, int $mask = 32 ): Ip4SubnetEntity
    {
        $faker = FakerFactory::create();
        $subnet = new Ip4SubnetEntity();
        $subnet->setUuid( $faker->uuid() );
        $subnet->setIp( ($vmVo->netIp()->value())? $vmVo->netIp()->value()  :null);
        $subnet->setMask($mask);
        $subnet->setFloatgroup($floatgroupuuid);
        $subnet->setActive(true);
        return $subnet;
    }
    
    private function ip4Entity( Vm $vmVo ): Ip4Entity// NOSONAR
    {
        $faker = FakerFactory::create();


        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($faker->uuid());
        $networkEntity->setName($faker->name());
        $networkEntity->setNetmask('255.255.255.0');// NOSONAR
        $networkEntity->setGateway('192.168.3.1');// NOSONAR
        


        $vmEntity = new Ip4Entity();
        $vmEntity->setUuid($vmVo->uuid()->value());
        $vmEntity->setIp($vmVo->netIp()->value());
        $vmEntity->setNetwork($networkEntity);

        $reflection = new \ReflectionClass($vmEntity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);// NOSONAR
        $property->setValue($vmEntity, $faker->numberBetween(1, 100));// NOSONAR

        return $vmEntity;
    }

    private function arrayIp4Response( int $numIp = 2 ): array// NOSONAR
    {
        $faker = FakerFactory::create();

        $result = [];
        repeat(
            function() use ($faker, &$result) {
                $ip = new Ip4Response(
                    $faker->uuid(),
                    $faker->ipv4(),
                    null,
                    true,
                    $faker->numberBetween(1, 9)
                );
                $result[] = $ip;
            },
            $numIp
        );

        return $result;
    }
    

    private function FloatGroupArray( int $numIp = 2 ): array// NOSONAR
    {
        $faker = FakerFactory::create();

        $result = [];
        repeat(
            function() use ($faker, &$result) {
                $fg = [
                    'uuid' => $faker->uuid(),
                    'gcp_node_name' => $faker->name(),
                    'pve_node_name' => $faker->name(),
                    'priority' => $faker->numberBetween(1, 9),
                ];
                $result[] = $fg;
            },
            $numIp
        );

        return $result;
    }

    private function FloatGroupResponse( $floatgroupArray = [], $networkArray = [] ): FloatGroupResponse// NOSONAR
    {
        $faker = FakerFactory::create();

        return new FloatGroupResponse(
                    $faker->uuid(),
                    $faker->name(),
                    true,
                    null,
                    $networkArray,
                    $floatgroupArray
                );
    }

    private function vmEntity( Vm $vm, $nodeId): VmEntity
    {
        $vmEntity = new VmEntity();
        $vmEntity->setUuid($vm->uuid()->value());
        $vmEntity->setActive(true);
        $vmEntity->setName($vm->name()->value());
        $vmEntity->setIdNode($nodeId);
        $vmEntity->setOs($vm->os()->value());
        $vmEntity->setCores($vm->cpuCores()->value());
        $vmEntity->setIp($vm->netIp()->value());
        $vmEntity->setGateway($vm->netGw()->value());
        $vmEntity->setDiskSize($vm->diskSize()->value());
        $vmEntity->setVmid($vm->vmId()->value());
        $vmEntity->setMemory($vm->memory()->value());
        $vmEntity->setTrafficLimit($vm->trafficLimit()->value());
        $vmEntity->setUsername($vm->userName()->value());
        $vmEntity->setPassword($vm->password()->value());
        $now = new DateTime();
        $vmEntity->setCreatedAt($now);
        $vmEntity->setUpdatedAt($now);
        return $vmEntity;
    }
}
