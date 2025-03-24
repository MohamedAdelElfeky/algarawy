<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Chat\Services\ChatService;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;
use App\Domain\Services\PaginationService;
use App\Events\MessageSent;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\ChatMessageResource;
use Illuminate\Http\JsonResponse;

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

        $conversation = $this->chatService->createConversation($conversationDTO);

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
        broadcast(new MessageSent($message))->toOthers();

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

    public function getConversations(): JsonResponse
    {
        $perPage = request()->get('per_page', 10);
        $page = request()->get('page', 1);
        $conversations = $this->chatService->getConversations($perPage, $page);
        return response()->json([
            'message' => 'All conversations retrieved successfully',
            'data' => ConversationResource::collection($conversations),
            'pagination' => (new PaginationService)->getPaginationData($conversations),
        ]);
    }
}
