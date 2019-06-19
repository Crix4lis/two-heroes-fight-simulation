<?php
declare(strict_types=1);

namespace Emagia\Reader;

use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\MediatorPattern\Event;

interface GameReaderInterface extends ColleagueInterface
{
    public function printEvent(Event $event): void;
}
