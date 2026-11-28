<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Fixtures\Event;

use Yeremi\SchemaMapper\Attributes\ApiSchema;

final readonly class Event
{
    /**
     * @param EventImage[] $images
     */
    public function __construct(
        #[ApiSchema('id')]
        private string $id = '',
        #[ApiSchema('title')]
        private string $title = '',
        #[ApiSchema('rating')]
        private float $rating = 0.0,
        #[ApiSchema('images', EventImage::class, true)]
        private array $images = [],
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
