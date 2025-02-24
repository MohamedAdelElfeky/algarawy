<?php
namespace App\Applications\DTOs;

class ServiceDTO
{
    public function __construct(
        public ?string $description,
        public ?array $images_or_video,
        public ?string $location,
        public ?string $status
    ) {}
}
