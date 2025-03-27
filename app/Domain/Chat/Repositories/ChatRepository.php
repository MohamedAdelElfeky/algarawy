<?php

namespace App\Domain\Chat\Repositories;

use App\Domain\Chat\Models\Conversation;
use App\Domain\Chat\Models\Message;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;
use App\Domain\Chat\Models\ConversationParticipant;

class ChatRepository
{

    public function createConversation(ConversationDTO $dto)
    {
        $conversation = Conversation::create([
            'type' => $dto->type,
            'name' => $dto->name
        ]);

        $conversation->participants()->createMany(
            collect($dto->user_ids)->map(fn($id) => ['user_id' => $id])->toArray()
        );

        return $conversation;
    }

    public function sendMessage(MessageDTO $dto)
    {
        return Message::create([
            'conversation_id' => $dto->conversation_id,
            'user_id' => $dto->user_id,
            'message' => $dto->message
        ]);
    }

    public function getMessages(int $conversationId, ?int $perPage = null, ?int $page = null)
    {
        $query = Message::where('conversation_id', $conversationId)->with('user');

        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function getUserConversations(int $userId, ?int $perPage = null, ?int $page = null)
    {
        $query = Conversation::with('participants.user')
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId));

        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function getConversations(?int $perPage = null, ?int $page = null)
    {
        $query = Conversation::with('participants');

        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function getConversationParticipants(int $conversationId, ?int $perPage = null, ?int $page = null)
    {
        $query = ConversationParticipant::with('user')
            ->where('conversation_id', $conversationId);

        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }
}
