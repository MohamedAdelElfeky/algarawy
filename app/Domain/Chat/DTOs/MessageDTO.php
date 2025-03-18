<?php

namespace App\Domain\Chat\DTOs;

class MessageDTO
{
    public function __construct(
        public int $conversation_id,
        public int $user_id,
        public string $message
    ) {}
}
