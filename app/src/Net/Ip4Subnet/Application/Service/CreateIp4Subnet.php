<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use Exception;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupEntityByUuidQueried;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetIP;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\Const\Ip4\Tags;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4EntityQueried;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4WithRelationsQueried;
use GridCP\Net\Ip4Subnet\Domain\Exception\FloatgroupNotValidException;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetFloatgroupException;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpException;
use GridCP\Net\Ip4Subnet\Domain\Exception\IpsDuplicatedException;
use GridCP\Net\Ip4Subnet\Domain\Exception\NotValidTagIpException;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;



use Psr\Log\LoggerInterface;
use IPCalc\IP;
class CreateIp4Subnet
{
    use CalcIps;
    public function __construct(
        private readonly Ip4SubnetRepository $ip4SubnetRepository,
        public LoggerInterface       $logger,
        private QueryBus             $queryBus,
    )
    {
    }

    public function __invoke(Ip4SubnetVo $ip4SubnetVo): string
    {
        $this->logger->info("Service - Start POST Create IP4 Subnet");
        $floatgroup = $this->getFloatgroup( $ip4SubnetVo->subnetUUidFloatgroup() );
        
        $this->logger->info("Start Get All IP4 Subnet");
        $allSubnets = $this->ip4SubnetRepository->getAll();

        $allIps = $this->getAllIpsInArray($allSubnets);
        if ($ip4SubnetVo->subnetIP()) {
            $this->validateSubnets($ip4SubnetVo, $allIps,$floatgroup);
        }
        
        $this->logger->info("Start - Save in BD the new Subnet.");
        $subnet = new Ip4SubnetEntity();
        $subnet->setUuid( $ip4SubnetVo->subnetUUid()->value() );
        $subnet->setIp( ($ip4SubnetVo->subnetIP())? $ip4SubnetVo->subnetIP()->value()  :null);
        $subnet->setMask($ip4SubnetVo->subnetMask()->getValue());
        $subnet->setFloatgroup($floatgroup->getUuid());
        $subnet->setActive(true);

        try {
            $this->ip4SubnetRepository->save($subnet);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }

        return $subnet->getUuid();
    }
    private function validateSubnets( Ip4SubnetVo $ip4SubnetVo, array $allIps, $floatgroup): void
    {
        $ipSubnetWithCidr = $ip4SubnetVo->subnetIP()->value() . '/' .$ip4SubnetVo->subnetMask()->getValue();
        $this->logger->info("Start - Check if the input ip with cidr: " .$ipSubnetWithCidr ." is duplicated with the Existing subnet ip's.");
        $ipSubnetWithCidrCalIp = new IP($ipSubnetWithCidr);
        $ipsDuplicated = $this->findDuplicateIps( $ipSubnetWithCidrCalIp, $allIps);
        if (!empty($ipsDuplicated)) {
            throw new IpsDuplicatedException($ipsDuplicated);
        }

        $this->logger->info("Start - Check if the ip with cidr: " .$ipSubnetWithCidr ." exists in the Ip4 DB.");
        $this->existsIpWithCidr($ipSubnetWithCidrCalIp, $floatgroup);
    }

    private function existsIpWithCidr( IP $ipWithCidr, $floatgroup ):void
    {
        $ips = $this->getIp4s($ipWithCidr);
        array_walk($ips, function ($ip) use ($floatgroup) {

            $ipRelations = $this->getIpWithNetworks(new SubnetIP($ip));

            if ( !Tags::isValidTag($ipRelations['ip']['tag']) ) {
                throw new NotValidTagIpException($ip, $ipRelations['ip']['tag']);
            }
            if ($ipRelations['network']['floatGroups'][0]['uuid'] != $floatgroup->getUuid()) {
                throw new FloatgroupNotValidException();
            }

        });
    }

    private function getIpWithNetworks(?SubnetIP $ip)
    {
        if (!$ip) {
            return null;
        }
        
        $ipRelations = $this->queryBus->ask(new GetIp4WithRelationsQueried($ip->value()));
        $ipRelations=$ipRelations->gets();
        if (!$ipRelations) {
            throw new GetIpException($ip->value());
        }
        return $ipRelations;
    }

    private function getIp(?SubnetIP $ipSubnet): ?Ip4Entity
    {
        if (!$ipSubnet) {
            return null;
        }
        
        $ip = $this->queryBus->ask(new GetIp4EntityQueried($ipSubnet->value()));
        $ipEntity = $ip->get();
        if (!$ipEntity) {
            throw new GetIpException($ipSubnet->value());
        }
        return $ipEntity;
    }
    private function getFloatgroup(?UuidFloatgroup $uuidFg): ?Ip4FloatGroupEntity
    {
        if (!$uuidFg) {
            return null;
        }
        
        $floatgroup = $this->queryBus->ask(new GetFloatGroupEntityByUuidQueried($uuidFg->value()));
        $floatgroupEntity = $floatgroup->get();
        if (!$floatgroupEntity) {
            throw new GetFloatgroupException();
        }
        return $floatgroupEntity;
    }
    private function getAllIpsInArray(array $subnets): array
    {
        return array_map(function($subnet) {
            return $subnet->getIp() . '/' . $subnet->getMask();
        }, $subnets);
    }
}