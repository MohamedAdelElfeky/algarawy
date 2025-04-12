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
        $query = Message::where('conversation_id', $conversationId)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function getUserConversations(int $userId, ?int $perPage = null, ?int $page = null)
    {
        $query = Conversation::with('participants.user')
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->orderByRaw('(SELECT MAX(created_at) FROM messages WHERE messages.conversation_id = conversations.id) DESC');

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

    public function addParticipants(int $conversationId, array $userIds)
    {
        $conversation = Conversation::findOrFail($conversationId);

        foreach ($userIds as $userId) {
            $conversation->participants()->firstOrCreate(['user_id' => $userId]);
        }

        return $conversation->participants;
    }
    
    public function removeParticipants(int $conversationId, array $userIds)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $conversation->participants()->whereIn('user_id', $userIds)->delete();

        return $conversation->participants;
    }
}
