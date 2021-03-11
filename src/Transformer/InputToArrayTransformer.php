<?php

declare(strict_types=1);

namespace ConVal\Transformer;

use ConVal\Contract\InputToArrayTransformerInterface;
use ConVal\Contract\StringFunction;
use Symfony\Component\Console\Input\InputInterface;

class InputToArrayTransformer implements InputToArrayTransformerInterface
{
    private ?StringFunction $paramNameTransformer;

    public function __construct(?StringFunction $paramNameTransformer = null)
    {
        $this->paramNameTransformer = $paramNameTransformer;
    }

    public function transform(InputInterface $input): array
    {
        $merged = array_replace($input->getArguments(), $input->getOptions());

        if ($this->paramNameTransformer === null) {
            return $merged;
        }

        $rekeyed = [];
        foreach ($merged as $key => $value) {
            $newKey = $this->paramNameTransformer->apply((string)$key);
            $rekeyed[$newKey] = $value;
        }

        return $rekeyed;
    }
}