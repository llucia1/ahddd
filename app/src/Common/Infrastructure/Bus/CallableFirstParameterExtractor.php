<?php
declare(strict_types=1);

namespace GridCP\Common\Infrastructure\Bus;

use GridCP\Common\Domain\Bus\EventSource\DomainEventSubscriber;
use GridCP\Node\Application\Cqrs\Handlers\SearchNodeByNameQuerieHandler;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use JetBrains\PhpStorm\NoReturn;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\reindex;
use function Lambdish\Phunctional\map;

final class CallableFirstParameterExtractor
{
    public static function forPipedCallables(iterable $callables): array
    {
        return reduce(self::pipedCallablesReducer(), $callables, []);
    }

    private static function pipedCallablesReducer(): callable
    {
        return static function ($subscribers, DomainEventSubscriber $subscriber): array {
            $subscribedEvents = $subscriber::subscribedTo();

            foreach ($subscribedEvents as $subscribedEvent) {
                $subscribers[$subscribedEvent][] = $subscriber;
            }

            return $subscribers;
        };
    }

    public static  function  forCallables(iterable $callables):array{


        return map(self::unflatten(), reindex(self::classExtractor(new self()), $callables));
    }

    private static function unflatten():callable
    {

       return  static fn (mixed $value): array =>[$value];

    }
    private static function classExtractor(self $parameterExtractor): callable{

         $result = static fn (object $handler): ?string => $parameterExtractor->extract($handler);
        return $result;
    }

    public function extract(object $class):?string
    {

        $reflector = new ReflectionClass($class);

        $method = $reflector->getMethod('__invoke');
        var_dump('MÉTODO --->' . $method);
        if ( $this->hasOnlyOneParameter($method)){
            var_dump("METHOD ->" . $method);
            return $this->firstParameterClassFrom($method);
        }

        return  null;
    }

    private function firstParameterClassFrom(\ReflectionMethod $method):string
    {
        /** @var ReflectionNamedType|null $fistParameterType */
        $fistParameterType = $method->getParameters()[0]->getType();

        if ($fistParameterType === null) {
            throw new LogicException('Missing type hint for the first parameter of __invoke');
        }

        return $fistParameterType->getName();
    }
    private function hasOnlyOneParameter(\ReflectionMethod $method): bool
    {
        return $method->getNumberOfParameters() ===1;
    }

}