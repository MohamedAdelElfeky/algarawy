<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Chat\Services\ChatService;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;
use App\Domain\Chat\Models\Conversation;
use App\Domain\Services\PaginationService;
use App\Events\MessageSent;
use App\Http\Requests\AddParticipantsRequest;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ConversationParticipantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService)
    {
        $this->middleware('auth:sanctum');
    }

    public function createConversation(CreateConversationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $conversationDTO = new ConversationDTO(
            type: $validatedData['type'],
            name: $validatedData['name'],
            user_ids: $validatedData['user_ids']
        );

        $conversation = $this->chatService->createConversation($conversationDTO, $request);
        $this->chatService->addParticipants($conversation->id, $validatedData['user_ids']);
        return response()->json([
            'message' => 'تم إنشاء المحادثة بنجاح.',
            'data' => new ConversationResource($conversation)
        ], 201);
    }

    public function sendMessage(ChatMessageRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $dto = new MessageDTO(
            $validatedData['conversation_id'],
            auth()->id(),
            $validatedData['message']
        );

        $message = $this->chatService->sendMessage($dto);
        return response()->json([
            'message' => 'تم إرسال الرسالة بنجاح.',
            'data' => new ChatMessageResource($message)
        ]);
    }

    public function getMessages($conversationId): JsonResponse
    {
        $perPage = request()->get('per_page', 10);
        $page = request()->get('page', 1);

        $messages = $this->chatService->getMessages($conversationId, $perPage, $page);

        return response()->json([
            'message' => 'تم استرجاع الرسائل بنجاح',
            'data' => ChatMessageResource::collection($messages),
            'pagination' => (new PaginationService)->getPaginationData($messages),
        ]);
    }

    public function getUserConversations(): JsonResponse
    {
        $userId = auth()->id();
        $perPage = request()->get('per_page', 10);
        $page = request()->get('page', 1);
        $conversations = $this->chatService->getUserConversations($userId, $perPage, $page);
        return response()->json([
            'message' => 'User conversations retrieved successfully',
            'data' => ConversationResource::collection($conversations),
            'pagination' => (new PaginationService)->getPaginationData($conversations),
        ]);
    }

    public function getConversationParticipants(int $conversationId): JsonResponse
    {
        $perPage = request()->get('per_page', 10);
        $page = request()->get('page', 1);

        $participants = $this->chatService->getConversationParticipants($conversationId, $perPage, $page);

        return response()->json([
            'message' => 'Participants retrieved successfully',
            'data' => ConversationParticipantResource::collection($participants),
            'pagination' => (new PaginationService())->getPaginationData($participants)
        ]);
    }

    public function addParticipantsToConversation(AddParticipantsRequest $request, $conversationId): JsonResponse
    {
        $participants = $this->chatService->addParticipantsToConversation($conversationId, $request->user_ids);
        $this->chatService->addParticipants($conversationId, $request->user_ids);
        return response()->json([
            'message' => 'تمت إضافة المستخدمين بنجاح',
            'data' => ConversationParticipantResource::collection($participants)
        ]);
    }

    public function updatePhoto(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'nulled|string|max:255',
        ]);

        $updatedConversation = $this->chatService->updateConversationPhoto($request, $conversation);

        return response()->json([
            'message' => 'تم تحديث صورة المحادثة بنجاح.',
            'data' => new ConversationResource($updatedConversation),
        ]);
    }

    public function removeParticipantsFromConversation(Request $request, $conversationId): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $participants = $this->chatService->removeParticipantsFromConversation($conversationId, $request->user_ids);

        return response()->json([
            'message' => 'تمت إزالة المستخدمين بنجاح',
            'data' => ConversationParticipantResource::collection($participants)
        ]);
    }
}
