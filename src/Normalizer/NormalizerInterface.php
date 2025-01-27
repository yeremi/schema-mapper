<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Normalizer;

interface NormalizerInterface
{
    /**
     * @param array<string, mixed> $data
     * @param string $targetClass
     * @return object
     */
    public function normalize(array $data, string $targetClass): object;
}
