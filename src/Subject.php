<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\ObserverPattern\Event;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\ObserverPattern\SubjectInterface;

/**
 * Base class for unit class that needs to notify interested classes when it changes its inner state
 */
abstract class Subject implements SubjectInterface
{
    /** @var ObserverInterface[] */
    protected $observers = [];

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
