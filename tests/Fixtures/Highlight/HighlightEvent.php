<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures\Highlight;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class HighlightEvent
{
    public function __construct(
        #[ApiSchema('id')]
        private string $id = ''
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
