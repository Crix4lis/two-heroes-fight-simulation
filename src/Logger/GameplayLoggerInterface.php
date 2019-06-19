<?php
declare(strict_types=1);

namespace Emagia\Logger;


use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\MediatorPattern\Event;

/**
 * Logs all events from Emagia\Event\
 */
interface GameplayLoggerInterface extends ColleagueInterface
{
    public function logEvent(Event $event): void;
}
