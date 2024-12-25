<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Normalizer;

interface NormalizerInterface
{
    public function normalize(array $data, string $targetClass): object;
}
