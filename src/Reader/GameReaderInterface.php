<?php
declare(strict_types=1);

namespace Emagia\Reader;

use Emagia\MediatorPattern\Event;

interface GameReaderInterface
{
    public function printEvent(Event $event): void;
}
