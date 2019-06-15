<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Unit\UnitInterface;

interface AttackerResolverInterface
{
    /**
     * @param UnitInterface $firstUnit
     * @param UnitInterface $secondUnit
     *
     * @return UnitInterface
     *
     * @throws AttackResolverException
     */
    public function resolveAttacker(UnitInterface $firstUnit, UnitInterface $secondUnit): UnitInterface;
}
