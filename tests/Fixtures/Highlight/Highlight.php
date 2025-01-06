<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures\Highlight;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

class Highlight
{
    public function __construct(
        #[ApiSchema('event', HighlightEvent::class)]
        private ?HighlightEvent $event = null
    ) {
    }

    public function getEvent(): ?HighlightEvent
    {
        return $this->event;
    }
}
