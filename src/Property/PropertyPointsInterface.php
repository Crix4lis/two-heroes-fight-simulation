<?php
declare(strict_types=1);

namespace Emagia\Property;

interface PropertyPointsInterface
{
    public function getPoints(): int;
    public function __toString();
}
