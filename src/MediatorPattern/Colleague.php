<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

trait Colleague
{
    /** @var EventAndLogsMediatorInterface|null */
    private $mediator;

    public function setMediator(EventAndLogsMediatorInterface $mediator): void
    {
        if ($this->mediator !== null) {
            throw new ColleagueException('Mediator is already set! Cannot override mediator!');
        }

        $this->mediator = $mediator;
    }

    public function getMediator(): EventAndLogsMediatorInterface
    {
        if ($this->mediator === null) {
            throw new ColleagueException('Mediator is missing!');
        }

        return $this->mediator;
    }
}
