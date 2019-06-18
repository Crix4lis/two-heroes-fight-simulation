<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

interface ColleagueInterface
{
    public function setMediatior(MediatorInterface $mediator): void;
}
