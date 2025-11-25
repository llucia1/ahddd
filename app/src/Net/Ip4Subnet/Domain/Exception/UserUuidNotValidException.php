<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class UserUuidNotValidException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Uuid User Not Valid.'));
    }
}