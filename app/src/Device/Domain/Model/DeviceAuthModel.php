<?php
declare(strict_types=1);

namespace GridCP\Device\Domain\Model;

use Error;
use GridCP\Device\Domain\Exception\CreateDeviceEventError;
use GridCP\Node\Domain\Exception\CreateNodeEventError;

class DeviceAuthModel
{



    public function __construct(
                                    private ?int $id = null,
                                    public ?string $uuid = null,
                                    public ?string $ip = null,
                                    public ?string $device = null,
                                    public ?string $country = null,
                                    public ?string $location = null,
                               )
    {

    }
    public static function create(
                                    ?string $uuid,
                                    ?string $ip,
                                    ?string $device,
                                    ?string $country,
                                    ?string $location,
                                    ?int $id = null,
                                  ): self
    {
        try {
            $node = new self(
                                $uuid,
                                $ip,
                                $device,
                                $country,
                                $location,
                                $id

            );
            //$node->record(new NodeCreatedDomainEvent(UuidValueObject::random()->value(), $uuid->value(), $name->value(), $hostName->value(), $ip->value(), $sshPort->value(), $timeZone->value(), $keyboard->value(), $display->value(), $storage->value(), $storageIso->value(), $storageImage->value(), $storageBackup->value(), $networkInterface->value()));
            return $node;
        } catch (Error $e) {
            throw new CreateDeviceEventError($e);
        }
    }
}