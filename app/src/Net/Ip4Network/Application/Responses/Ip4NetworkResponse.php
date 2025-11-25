<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Application\Responses;


use GridCP\Net\Ip4FloatGroup\Domain\Model\Ip4FloatGroupModel;

final readonly class Ip4NetworkResponse
{
 public function __construct(private ?string $uuid, private ?string $name, private ?string $name_server_1,
                             private ?string $name_server_2, private ?string $name_server_3, private ?string $name_server_4,
                             private ?int $priority, private ?string $netMask, private ?string $gateway,
                             private ?string $broadcast, private ?Ip4FloatGroupModel $floatGroup, private ?int $id=null){}

    public function id():?int
    {
        return $this->id;
    }

    public function uuid():?string
    {
        return $this->uuid;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function name_server_1():?string
    {
        return $this->name_server_1;
    }

    public function name_server_2():?string
    {
        return $this->name_server_2;
    }
    public function name_server_3():?string
    {
        return $this->name_server_3;
    }

    public function name_server_4():?string
    {
        return $this->name_server_4;
    }


    public function priority(): ?int
    {
        return $this->priority;
    }
    public function netMask(): ?string
    {
        return $this->netMask;
    }

    public function gateway():?string
    {
        return $this->gateway;
    }

    public function broadcast():?string
    {
        return $this->broadcast;
    }

    public function floatGroup():?Ip4FloatGroupModel
    {
        return $this->floatGroup;
    }
}