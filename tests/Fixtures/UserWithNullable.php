<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserWithNullable
{
    #[ApiSchema('description')]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
