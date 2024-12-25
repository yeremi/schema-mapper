<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class SimpleUser
{
    #[ApiSchema('name')]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }
}
