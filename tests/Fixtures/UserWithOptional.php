<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserWithOptional
{
    #[ApiSchema('email')]
    private ?string $email = null;

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
