<?php

declare(strict_types=1);

namespace ConVal\Contract;

interface StringFunction
{
    public function apply(string $inputString): string;
}