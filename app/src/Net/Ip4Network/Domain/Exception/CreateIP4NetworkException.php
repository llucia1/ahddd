<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Domain\Exception;

final class CreateIP4NetworkException extends \Error
{
    public function __construct(\Error $e)
    {
        parent::__construct(sprintf('Error creating IP network', $e->getMessage()));
    }
}