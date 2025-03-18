<?php

namespace App\Http\Controllers;

use App\Domain\Chat\DTOs\MessageDTO;
use App\Domain\Chat\Services\ChatService;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\CreateConversationRequest;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    /**
     * Show chat view
     */
    public function index()
    {
        $conversations = $this->chatService->getConversations();
        return view('pages.dashboards.chat.index', compact('conversations'));
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(int $conversationId): JsonResponse
    {
        return response()->json($this->chatService->getMessages($conversationId));
    }

    /**
     * Send a new message
     */
    public function sendMessage(ChatMessageRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $dto = new MessageDTO(
            $validatedData['conversation_id'],
            auth()->id(),
            $validatedData['message'],
        );
        return response()->json([
            'status' => 'success',
            'message' => $this->chatService->sendMessage($dto),
        ]);
    }
    
    /**
     * Create a new conversation
     */
    public function createConversation(CreateConversationRequest $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'conversation' => $this->chatService->createConversation($request->validated()),
        ]);
    }
}
