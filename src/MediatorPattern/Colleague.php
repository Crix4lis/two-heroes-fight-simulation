<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

abstract class Colleague implements ColleagueInterface
{
    /** @var EventAndLogsMediatorInterface */
    protected $mediator;

    public function setMediatior(EventAndLogsMediatorInterface $mediator): void
    {
        $this->mediator = $mediator;
    }

    public function getMediator(): EventAndLogsMediatorInterface
    {
        return $this->mediator;
    }
}
