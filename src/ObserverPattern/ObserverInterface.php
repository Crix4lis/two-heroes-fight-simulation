<?php
declare(strict_types=1);

namespace Emagia\ObserverPattern;

/**
 * Class that is interested in subject's state change upun which might initialize different tasks.
 */
interface ObserverInterface
{
    public function update(Event $event): void;
}
