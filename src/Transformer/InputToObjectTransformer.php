<?php

declare(strict_types=1);

namespace ConVal\Transformer;

use ConVal\Contract\InputToArrayTransformerInterface;
use ConVal\Contract\InputToObjectTransformerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InputToObjectTransformer implements InputToObjectTransformerInterface
{
    private InputToArrayTransformerInterface $inputToArrayTransformer;
    private DenormalizerInterface $denormalizer;

    public function __construct(
        InputToArrayTransformerInterface $inputToArrayTransformer,
        DenormalizerInterface $denormalizer
    ) {
        $this->inputToArrayTransformer = $inputToArrayTransformer;
        $this->denormalizer = $denormalizer;
    }

    public function transform(InputInterface $input, string $targetClass): object
    {
        return $this->denormalizer->denormalize(
            $this->inputToArrayTransformer->transform($input),
            $targetClass,
        );
    }
}