<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Unit\UnitInterface;
use LogicException;

/**
 * Imitates domain service
 */
interface TurnServiceInterface
{
    /**
     * @param UnitInterface $attacker
     * @param UnitInterface $defender
     *
     * @throws LogicException
     */
    public function make(UnitInterface $attacker, UnitInterface $defender): void;
}
