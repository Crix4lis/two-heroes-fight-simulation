<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

trait Colleague
{
    /** @var MediatorInterface */
    private $mediator;

    public function setMediatior(MediatorInterface $mediator): void
    {
        $this->mediator = $mediator;
    }
}
