<?php

namespace ConVal\Contract;

use Symfony\Component\Console\Input\InputInterface;

interface InputToObjectTransformerInterface
{
    public function transform(InputInterface $input, string $targetClass): object;
}