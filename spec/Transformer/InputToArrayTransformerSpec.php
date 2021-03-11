<?php

declare(strict_types=1);

namespace spec\ConVal\Transformer;

use ConVal\Contract\InputToArrayTransformerInterface;
use ConVal\Contract\StringFunction;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputInterface;

class InputToArrayTransformerSpec extends ObjectBehavior
{
    public function it_is_an_input_to_array_transformer(): void
    {
        $this->shouldBeAnInstanceOf(InputToArrayTransformerInterface::class);
    }

    public function it_returns_empty_array_given_empty_input(InputInterface $input): void
    {
        $input->getArguments()->willReturn([]);
        $input->getOptions()->willReturn([]);
        $this->transform($input)->shouldReturn([]);
    }

    public function it_returns_all_arguments(InputInterface $input): void
    {
        $arguments = ['first-key' => 'first value', 'second-key' => 'second value'];
        $input->getArguments()->willReturn($arguments);
        $input->getOptions()->willReturn([]);
        $this->transform($input)->shouldReturn($arguments);
    }

    public function it_returns_all_options(InputInterface $input): void
    {
        $options = ['first-key' => 'first value', 'second-key' => 'second value'];
        $input->getArguments()->willReturn([]);
        $input->getOptions()->willReturn($options);
        $this->transform($input)->shouldReturn($options);
    }

    public function it_merges_arguments_and_options(InputInterface $input): void
    {
        $input->getArguments()->willReturn([
            'a' => 'A',
            'b' => true,
        ]);

        $input->getOptions()->willReturn([
            'c' => ['hello', 'world'],
            'd' => false,
        ]);

        $this->transform($input)->shouldReturn([
            'a' => 'A',
            'b' => true,
            'c' => ['hello', 'world'],
            'd' => false,
        ]);
    }

    public function it_applies_parameter_name_transformer(InputInterface $input): void
    {
        $input->getArguments()->willReturn(['abc' => 'value']);
        $input->getOptions()->willReturn(['123' => 'value']);
        $this->beConstructedWith(new StringReverser());
        $this->transform($input)->shouldReturn(['cba' => 'value', '321' => 'value']);
    }
}

class StringReverser implements StringFunction
{
    public function apply(string $inputString): string
    {
        return strrev($inputString);
    }
}