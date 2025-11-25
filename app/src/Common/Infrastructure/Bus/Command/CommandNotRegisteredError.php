<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\Command;


use GridCP\Common\Domain\Bus\Command\Command;

final class CommandNotRegisteredError extends  \RuntimeException
{
    public function __construct(Command $command)
    {
        $commandClass = $command::class;
        parent::__construct("The Command <c$commandClass> hasn't command handler associated :(");
    }

}