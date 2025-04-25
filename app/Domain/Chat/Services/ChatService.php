<?php

namespace App\Domain\Chat\Services;

use App\Domain\Chat\Repositories\ChatRepository;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;
use App\Domain\Chat\Models\Conversation;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesSingleImageUpload;
use App\Shared\Traits\PushNotificationOnly;
use Illuminate\Http\Request;

class ChatService
{
    use HandlesSingleImageUpload, HandlesFileDeletion, PushNotificationOnly;

    public function __construct(private ChatRepository $chatRepository, private FirestoreService $firestoreService) {}

    public function createConversation(ConversationDTO $dto, Request $request)
    {
        $conversation = $this->chatRepository->createConversation($dto);

        $this->uploadSingleImage($request, $conversation, 'conversations', 'conversation', 'image', 'profile');

        return $conversation;
    }

    public function sendMessage(MessageDTO $dto)
    {
        $message = $this->chatRepository->sendMessage($dto);
        $this->firestoreService->storeMessage($message);
        $participants = $message->conversation->participants()->with('user.devices')->get();
        $otherUsers = $participants->pluck('user')->where('id', '!=', $dto->user_id);

        $this->sendFCMNotificationToUsers(
            $otherUsers->all(),
            'رسالة جديدة',
            $dto->message
        );
        return $message;
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

    public function getConversationParticipants(int $conversationId, ?int $perPage = null, ?int $page = null)
    {
        return $this->chatRepository->getConversationParticipants($conversationId, $perPage, $page);
    }

    public function addParticipantsToConversation(int $conversationId, array $userIds)
    {
        return $this->chatRepository->addParticipants($conversationId, $userIds);
    }

    public function addParticipants($conversationId, array $userIds)
    {
        $this->firestoreService->addParticipants($conversationId, $userIds);
    }

    public function updateConversationPhoto(Request $request, Conversation $conversation)
    {
        if ($request->filled('name')) {
            $conversation->name = $request->input('name');
            $conversation->save();
        }
        if ($request->hasFile('image')) {
            $oldImage = $conversation->images()->first();

            if ($oldImage) {
                $this->deleteFiles([$oldImage->id], 'image');
            }

            $this->uploadSingleImage($request, $conversation, 'conversations', 'conversation', 'image', 'profile');
        }

        return $conversation->load('images');
    }

    public function removeParticipantsFromConversation(int $conversationId, array $userIds)
    {
        return $this->chatRepository->removeParticipants($conversationId, $userIds);
    }
}
