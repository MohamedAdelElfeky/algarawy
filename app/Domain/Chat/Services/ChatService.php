<?php

namespace App\Domain\Chat\Services;

use App\Domain\Chat\Repositories\ChatRepository;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;

class ChatService
{
    protected ChatRepository $chatRepository;

    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }
   
    public function createConversation(ConversationDTO $dto)
    {
        return $this->chatRepository->createConversation($dto);
    }

    public function sendMessage(MessageDTO $dto)
    {
        return $this->chatRepository->sendMessage($dto);
    }

    public function getMessages(int $conversationId, ?int $perPage = null, ?int $page = null)
    {
        return $this->chatRepository->getMessages($conversationId, $perPage, $page);
    }
    
    public function getUserConversations(int $userId, ?int $perPage = null, ?int $page = null)
    {
        return $this->chatRepository->getUserConversations($userId, $perPage, $page);
    }
    
    public function getConversations(?int $perPage = null, ?int $page = null)
    {
        return $this->chatRepository->getConversations($perPage, $page);
    }
    
    
}
