<?php

declare(strict_types=1);

namespace spec\ConVal\Transformer;

use ConVal\Contract\InputToArrayTransformerInterface;
use ConVal\Contract\InputToObjectTransformerInterface;
use PhpSpec\ObjectBehavior;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InputToObjectTransformerSpec extends ObjectBehavior
{
    public function let(
        InputToArrayTransformerInterface $inputToArrayTransformer,
        DenormalizerInterface $denormalizer
    ): void {
        $this->beConstructedWith($inputToArrayTransformer, $denormalizer);
    }

    public function it_is_an_input_to_object_transformer(): void
    {
        $this->shouldBeAnInstanceOf(InputToObjectTransformerInterface::class);
    }

    public function it_transforms_input_to_array_then_to_object(
        InputToArrayTransformerInterface $inputToArrayTransformer,
        DenormalizerInterface $denormalizer,
        InputInterface $input
    ): void {
        $arrayResult = ['key' => 'value'];
        $objectResult = new stdClass();

        $inputToArrayTransformer
            ->transform($input)
            ->shouldBeCalledOnce()
            ->willReturn($arrayResult);

        $denormalizer
            ->denormalize($arrayResult, stdClass::class)
            ->shouldBeCalledOnce()
            ->willReturn($objectResult);

        $this->transform($input, stdClass::class)->shouldReturn($objectResult);
    }
}