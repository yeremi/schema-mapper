<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures\Event;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

final class Event
{
    /**
     * @param EventImage[] $images
     */
    public function __construct(
        #[ApiSchema('id')]
        private readonly string $id = '',
        #[ApiSchema('title')]
        private readonly string $title = '',
        #[ApiSchema('rating')]
        private readonly float $rating = 0.0,
        #[ApiSchema('images', EventImage::class, true)]
        private readonly array $images = [],
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @return EventImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
