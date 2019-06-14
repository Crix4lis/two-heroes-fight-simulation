<?php
declare(strict_types=1);

namespace Emagia\ObserverPattern;

/**
 * Class that when changes its state needs to notify different loosely coupled classes
 * about its current state, not even knowing what and how many different classes will be interested
 * in its state.
 */
interface SubjectInterface
{
    public function register(ObserverInterface $observer): void;
    public function notifyObservers(Event $event): void;
}
