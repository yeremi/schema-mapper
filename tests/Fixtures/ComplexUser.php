<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class ComplexUser
{
    #[ApiSchema('first_name')]
    private string $firstName;

    #[ApiSchema('last_name')]
    private string $lastName;

    #[ApiSchema('age')]
    private int $age;

    #[ApiSchema('is_active')]
    private bool $isActive;

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
