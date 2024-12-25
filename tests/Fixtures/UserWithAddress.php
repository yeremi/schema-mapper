<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserWithAddress
{
    #[ApiSchema('address', Address::class)]
    private Address $address;

    public function getAddress(): Address
    {
        return $this->address;
    }
}
