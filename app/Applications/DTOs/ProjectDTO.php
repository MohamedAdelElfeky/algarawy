<?php
namespace App\Applications\DTOs;

class ProjectDTO
{
    public function __construct(
        public ?string $description,
        public ?string $location,
        public ?string $status
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['description'] ?? null,
            $data['location'] ?? null,
            $data['status'] ?? null
        );
    }
}
