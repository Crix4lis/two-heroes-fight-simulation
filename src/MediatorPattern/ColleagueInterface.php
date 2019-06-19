<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

interface ColleagueInterface
{
    public function setMediator(EventAndLogsMediatorInterface $mediator): void;
    public function getMediator(): EventAndLogsMediatorInterface;
}
