<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ApiSchema
{
    public function __construct(
        private readonly string  $key,
        private readonly ?string $targetClass = null,
        private readonly bool $isArray = false
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTargetClass(): ?string
    {
        return $this->targetClass;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }
}
