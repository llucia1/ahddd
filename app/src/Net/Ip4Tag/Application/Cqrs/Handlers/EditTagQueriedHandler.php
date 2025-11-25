<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Tag\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Common\Domain\ValueObjects\Ip4TagActive;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Net\Ip4Tag\Application\Cqrs\Queries\EditTagQueried;
use GridCP\Net\Ip4Tag\Application\Response\ChangeIp4TagResponse;
use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;
use GridCP\Net\Ip4Tag\Application\Service\EditIP4Tag;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagNotExits;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuidIp;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class EditTagQueriedHandler implements QueryHandler
{

    public function __construct(private  readonly  EditIP4Tag $tagService)
    {
    }


    public function __invoke(EditTagQueried $tag): ChangeIp4TagResponse
    {
        try {
            $uuidVo = new Ip4TagUuid( $tag->uuid());
            $uuidIp = new Ip4TagUuidIp( $tag->uuidIp() );
            $tagVo = $tag->tag() ?  new Ip4TagTag( $tag->tag() ) : null;
            $activeVo = is_null($tag->active()) ?  null : new Ip4TagActive( $tag->active() );

            $ip4Tag = new Ip4Tag($uuidVo, $uuidIp, $tagVo, $activeVo);
            $this->tagService->editIp4Tag($ip4Tag);
            return new ChangeIp4TagResponse($tag->uuid(), $tag->tag());
        } catch(Ip4TagNotExits $ex){
            throw new Ip4TagNotExits();
        } catch(\Exception $ex){
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage());

        }
    }
}