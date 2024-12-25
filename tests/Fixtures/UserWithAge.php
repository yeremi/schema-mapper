<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserWithAge
{
    #[ApiSchema('age')]
    private int $age;
}
