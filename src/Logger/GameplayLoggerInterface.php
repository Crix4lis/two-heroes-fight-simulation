<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\MediatorPattern\Event;

/**
 * Logs all events from Emagia\Event\
 */
interface GameplayLoggerInterface
{
    public function logEvent(Event $event): void;
}
