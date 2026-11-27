<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures\Event;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

final class EventImage
{
    public function __construct(
        #[ApiSchema('url')]
        private readonly string $url = '',
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
