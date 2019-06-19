<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\Unit\UnitInterface;

interface ErrorLoggerInterface extends ColleagueInterface
{
    public function logCriticalAttackerIsDead(UnitInterface $attacker, UnitInterface $defender): void;
    public function logErrorCannotResolveAttacker(): void;
}
