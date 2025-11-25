<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Bus\Query;

use GridCP\Common\Domain\Bus\Query\Query;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\Bus\Query\Response;
use GridCP\Common\Infrastructure\Bus\CallableFirstParameterExtractor;
use GridCP\Common\Infrastructure\Logger\MonoLogLogger;
use GridCP\Node\Application\Cqrs\Handlers\SearchNodeByNameQuerieHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

final readonly class InMemorySymfonyQueryBus implements QueryBus
{
    //private MessageBus $bus;


    /**
     * @throws \Exception
     */
    public function  __construct( private  readonly  MessageBusInterface $bus)
        //iterable $queryHandlers=[])
    {
  /*      $container = new ContainerBuilder();
        $queryHandlers = $container->findTaggedServiceIds( 'gridcp.search_node_by_name_querie');
        $this->bus = new MessageBus(
            [
                new HandleMessageMiddleware(
                    new HandlersLocator(CallableFirstParameterExtractor::forCallables($queryHandlers)
                    )
                ),
            ]
        );*/
    }
    public function ask(Query $query): ?Response
    {


       try{
           /** @var HandledStamp $stamp */
           $stamp = $this->bus->dispatch($query)->last(HandledStamp::class);

           return $stamp->getResult();
       }catch  (NoHandlerForMessageException){
            throw new QueryNotRegisteredError($query);
       }
    }
}