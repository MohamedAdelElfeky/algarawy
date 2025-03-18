<?php

namespace App\Domain\Chat\DTOs;

class ConversationDTO
{
    public function __construct(
        public string $type,
        public ?string $name,
        public array $user_ids
    ) {}
}
