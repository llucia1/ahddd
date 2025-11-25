<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Service;

use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\Service\IPatchFloatGroupService;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsPacth;
use Psr\Log\LoggerInterface;

readonly class PatchFloatGroupService implements IPatchFloatGroupService
{
    public function __construct(
        private IIp4FloatGroupRepository $floatGroupRepositoy,
        private LoggerInterface $logger
    ){}
    public function __invoke( Ip4FloatGroupsPacth $floatGroup):void
    {
          $this->update($floatGroup);
    }


    public function update(Ip4FloatGroupsPacth $floatGroup):void
    {

            $this->logger->info("Update Patch Service Float Group ->" . $floatGroup->uuid()->value());
            $floatGroupEntity = $this->floatGroupRepositoy->getByUuid($floatGroup->uuid()->value());
            if (!$floatGroupEntity || !$floatGroupEntity->isActive()){
                $this->logger->error("Error Float Group Not Exist" . $floatGroup->uuid()->value());
                throw new ErrorFloatGroupNotExist;
            }
            !is_null($floatGroup->name())  ? $floatGroupEntity->setName( $floatGroup->name()->value() ) : null;

            $this->floatGroupRepositoy->save($floatGroupEntity);
         
    }
}