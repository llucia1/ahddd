<?php
declare(strict_types=1);

namespace Tests\Plan\Application\Helper;

use GridCP\Plan\Domain\VO\Plan;
use GridCP\Plan\Domain\VO\PlanClosePorts;
use GridCP\Plan\Domain\VO\PlanCores;
use GridCP\Plan\Domain\VO\PlanDiskSize;
use GridCP\Plan\Domain\VO\PlanMemory;
use GridCP\Plan\Domain\VO\PlanName;
use GridCP\Plan\Domain\VO\PlanPatch;
use GridCP\Plan\Domain\VO\PlanTrafficLimit;
use GridCP\Plan\Domain\VO\PlanUuid;
use Faker\Factory as FakerFactory;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;

trait PlanTools
{
    private function createPlanEntity(Plan $plan): PlanEntity
    {
        $planEntity = new PlanEntity(); 
        $planEntity->setUuid($plan->uuid()->value());
        $planEntity->setName($plan->name()->value());
        $planEntity->setDiskSize($plan->diskSize()->value());
        $planEntity->setCores($plan->cores()->value());
        $planEntity->setMemory($plan->memory()->value());
        $planEntity->settrafficlimit($plan->trafficLimit()->value());
        return $planEntity;
    }
    private function createPlan(): Plan
    {
        $faker = FakerFactory::create();

        $planUuid = new PlanUuid ( PlanUuid::random()->value() );
        $planName= new PlanName( $faker->name() );
        $planDiskSize = new PlanDiskSize( 10);
        $planCores= new PlanCores( 1 );
        $planMemory= new PlanMemory( 2048);
        $planTrafficLimit= new PlanTrafficLimit( 8000 );
        $planClosePorts = new PlanClosePorts( '25' );


        return Plan::create(
            $planUuid,
            $planName,
            $planDiskSize,
            $planCores,
            $planMemory,
            $planTrafficLimit,
        );
    }
    private function createPlanPatch(): PlanPatch
    {
        $faker = FakerFactory::create();

        $planUuid = new PlanUuid ( PlanUuid::random()->value() );
        $planName= (rand(0, 1) == 0)? new PlanName( $faker->name() ) : null;
        $planDiskSize = (rand(0, 1) == 0)? new PlanDiskSize( 10 ) : null;
        $planCores = (rand(0, 1) == 0)? new PlanCores( 10) : null;
        $planMemory = (rand(0, 1) == 0)? new PlanMemory( 4096) : null;
        $planTrafficLimit = (rand(0, max: 1) == 0)? new PlanTrafficLimit( 8000) : null;


        return PlanPatch::create(
            $planUuid,
            $planName,
            $planDiskSize,
            $planCores,
            $planMemory,
            $planTrafficLimit,
        );
    }
}
