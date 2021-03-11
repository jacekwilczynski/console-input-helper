<?php

namespace ConVal\Contract;

use Symfony\Component\Console\Input\InputInterface;

interface InputToArrayTransformerInterface
{
    public function transform(InputInterface $input): array;
}