<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\MediatorPattern\Colleague;
use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\Unit\UnitInterface;

class AttackerResolver implements ColleagueInterface
{
    use Colleague;

    public function resolveAttacker(UnitInterface $firstUnit, UnitInterface $secondUnit): UnitInterface
    {
        if (!$firstUnit->getSpeed()->isEqual($secondUnit->getSpeed())) {
            return $this->getFastest($firstUnit, $secondUnit);
        }

        if (!$firstUnit->getLuck()->isEqual($secondUnit->getLuck())) {
            return $this->getLuckiest($firstUnit, $secondUnit);
        }

        $this->getMediator()->logErrorCannotResolveAttacker();
        throw new AttackResolverException('Cannot resolve attacker! Both units have the same speed and luck');
    }

    private function getFastest(UnitInterface $u1, UnitInterface $u2): UnitInterface
    {
        return $u1->getSpeed()->isGreater($u2->getSpeed()) ? $u1 : $u2;
    }

    private function getLuckiest(UnitInterface $u1, UnitInterface $u2): UnitInterface
    {
        return $u1->getLuck()->isGreater($u2->getLuck()) ? $u1 : $u2;
    }
}
