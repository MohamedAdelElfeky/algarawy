<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Chat\Services\ChatService;
use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\DTOs\ConversationDTO;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\CreateConversationRequest;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{

    public function __construct(private ChatService $chatService) {
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
        return response()->json([
            'message' => $this->chatService->createConversation($conversationDTO),
            'data' => $conversationDTO
        ], 201);
    }

    public function sendMessage(ChatMessageRequest $request)
    {
        $validatedData = $request->validated();
        $dto = new MessageDTO(
            $validatedData['conversation_id'],
            auth()->id(),
            $validatedData['message'],
        );

        return response()->json($this->chatService->sendMessage($dto));
    }

    public function getMessages($conversationId)
    {
        return response()->json($this->chatService->getMessages($conversationId));
    }
}
