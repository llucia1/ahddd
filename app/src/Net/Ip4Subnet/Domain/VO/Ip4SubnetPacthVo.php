<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Domain\VO;

class Ip4SubnetPacthVo
{
    public function __construct(
        private readonly ?SubnetUuid         $subnetUUid,
        private readonly ?UuidFloatgroup $subnetUUidFloatgroup,
        private readonly ?SubnetMask         $subnetMask,
        private readonly ?SubnetIP       $subnetIP
    )
    {
    }

    public function create(
        
        ?SubnetUuid         $subnetUUid,
        ?UuidFloatgroup $subnetUUidFloatgroup,
        ?SubnetMask         $subnetMask,
        ?SubnetIP       $subnetIP

    ): self
    {
        return new self(
            $subnetUUid, $subnetUUidFloatgroup, $subnetMask,
        $subnetIP);
    }

    public function subnetUUid(): ?SubnetUuid
    {
        return $this->subnetUUid;
    }

    public function subnetUUidFloatgroup(): ?UuidFloatgroup
    {
        return $this->subnetUUidFloatgroup;
    }

    public function subnetMask(): ?SubnetMask
    {
        return $this->subnetMask;
    }

    public function subnetIP(): ?SubnetIP
    {
        return $this->subnetIP;
    }
}