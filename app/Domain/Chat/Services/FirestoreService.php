<?php

namespace App\Domain\Chat\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use App\Domain\Chat\Models\Conversation;
use App\Domain\Chat\Models\Message;
use App\Http\Resources\ChatUserResource;

class FirestoreService
{
    protected FirestoreClient $firestore;

    public function __construct()
    {
        $this->firestore = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/serviceAccountKey.json'))
            ->createFirestore()
            ->database();
    }

    public function syncConversation(Conversation $conversation, Message $message = null)
    {
        $conversationId = (string) $conversation->id;

        $participants = $conversation->participants()->pluck('user_id')->map(fn($id) => (string) $id)->toArray();

        $data = [
            'id' => $conversationId,
            'type' => $conversation->type,
            'name' => $conversation->name,
            'participants' => $participants,
            'image' => $conversation->image ?? null,
            'created_at' => $conversation->created_at->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        if ($message) {
            $data['last_message'] = [
                'id' => (string) $message->id,
                'message' => $message->message,
                'user' => (new ChatUserResource($message->user))->resolve(),
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $this->firestore->collection('conversations')
            ->document($conversationId)
            ->set($data);
    }

    public function storeMessage(Message $message)
    {
        $conversation = $message->conversation;

        if (!$conversation) {
            return;
        }

        $conversationId = (string) $conversation->id;
        $messageId = (string) $message->id;
        $this->syncConversation($conversation, $message);
        $this->firestore->collection('conversations')
            ->document($conversationId)
            ->collection('messages')
            ->document($messageId)
            ->set([
                'id' => $messageId,
                'conversation_id' => $conversationId,
                'user' => (new ChatUserResource($message->user))->resolve(),
                'message' => $message->message,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ]);
    }

    public function addParticipants($conversationId, array $userIds)
    {
        $conversationRef = $this->firestore->collection('conversations')->document((string) $conversationId);
        $snapshot = $conversationRef->snapshot();

        if (!$snapshot->exists()) {
            return;
        }

        $existingParticipants = $snapshot->data()['participants'] ?? [];
        $newParticipants = array_unique(array_merge($existingParticipants, $userIds));

        $conversationRef->set(['participants' => $newParticipants], ['merge' => true]);
    }
}
