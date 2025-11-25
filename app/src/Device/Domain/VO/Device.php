<?php
declare(strict_types=1);

namespace GridCP\Device\Domain\VO;

use Error;
use GridCP\Common\Domain\Aggregate\AggregateRoot;
use GridCP\Device\Domain\Exception\CreateDeviceEventError;

final class Device extends AggregateRoot
{
    public function __construct(
        private readonly DeviceUuid     $uuid,
        private readonly DeviceIp       $ip,
        private readonly DeviceDevice   $device,
        private readonly DeviceCountry  $country,
        private readonly DeviceLocation $location
    ) {}

    public static function create(
                                    DeviceUuid     $uuid,
                                    DeviceIp       $ip,
                                    DeviceDevice   $device,
                                    DeviceCountry  $country,
                                    DeviceLocation $location


                                ): self
    {
        try {
            $device = new self(
                                $uuid,
                                $ip,
                                $device,
                                $country,
                                $location   
                              );
            return $device;
        } catch (Error $e) {
            throw new CreateDeviceEventError($e);
        }
    }

    public function uuid(): DeviceUuid
    {
        return $this->uuid;
    }

    public function ip(): DeviceIp
    {
        return $this->ip;
    }

    public function device(): DeviceDevice
    {
        return $this->device;
    }

    public function country(): DeviceCountry
    {
        return $this->country;
    }

    public function location():DeviceLocation
    {
        return $this->location;
    }
}