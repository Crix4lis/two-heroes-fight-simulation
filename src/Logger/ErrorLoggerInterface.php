<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\Unit\UnitInterface;

interface ErrorLoggerInterface
{
    public function logCriticalAttackerIsDead(UnitInterface $attacker, UnitInterface $defender): void;
    public function logErrorCannotResolveAttacker(): void;
}
