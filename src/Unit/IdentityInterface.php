<?php
declare(strict_types=1);

namespace Emagia\Unit;

interface IdentityInterface
{
    public function isTheSameInstance(IdentityInterface $instance): bool;
    public function getIdentity(): string;
}
