<?php

declare(strict_types=1);

namespace ConVal\Transformer;

use ConVal\Contract\StringFunction;

class DashesAndUnderscoresToCamelCase implements StringFunction
{
    public function apply(string $inputString): string
    {
        return preg_replace_callback(
            '/[-_]([a-zA-Z]?)/',
            static fn(array $matches): string => strtoupper($matches[1]),
            $inputString,
        );
    }
}