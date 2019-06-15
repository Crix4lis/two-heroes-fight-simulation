<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\ObserverPattern\Event;
use Emagia\ObserverPattern\ObserverInterface;

/**
 * Base "class" for unit class that needs to notify interested classes when it changes its inner state
 */
trait Subject
{
    /** @var ObserverInterface[] */
    private $observers = [];

    public function register(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notifyObservers(Event $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}
