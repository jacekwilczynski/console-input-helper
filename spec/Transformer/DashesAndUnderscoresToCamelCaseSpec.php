<?php

declare(strict_types=1);

namespace spec\ConVal\Transformer;

use ConVal\Contract\StringFunction;
use PhpSpec\ObjectBehavior;

class DashesAndUnderscoresToCamelCaseSpec extends ObjectBehavior
{
    public function it_is_a_string_function(): void
    {
        $this->shouldBeAnInstanceOf(StringFunction::class);
    }

    public function it_does_not_affect_a_string_without_dashes_or_underscores(): void
    {
        $this->apply('hello123World')->shouldReturn('hello123World');
    }

    public function it_removes_dashes_and_underscores_and_capitalises_the_next_letter(): void
    {
        $this->apply('heLLo-123_wonderful-world')->shouldReturn('heLLo123WonderfulWorld');
    }
}