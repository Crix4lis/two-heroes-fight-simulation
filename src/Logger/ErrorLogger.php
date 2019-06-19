<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\Unit\UnitInterface;

class ErrorLogger extends BaseLogger implements ErrorLoggerInterface
{
    public function logCriticalAttackerIsDead(UnitInterface $attacker, UnitInterface $defender): void
    {
        $msg = sprintf(
            'Attacker %s is dead! That must not happen. Make sure you didn\'t brake the application',
            $attacker->getName()
        );
        $att = var_export($attacker, true);
        $deff = var_export($defender, true);

        $this->logger->critical($msg);
        $this->logger->critical('Attacker dump: ' . $att);
        $this->logger->critical('Defender dump: ' . $deff);
    }

    public function logErrorCannotResolveAttacker(): void
    {
        $this->logger->error('Cannot resolve attacker! Both units have the same speed and luck');
    }
}
