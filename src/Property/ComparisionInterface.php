<?php
declare(strict_types=1);

namespace Emagia\Property;

interface ComparisionInterface
{
    public function isGreater(PropertyPointsInterface $property): bool;
    public function isEqual(PropertyPointsInterface $property): bool;
}
