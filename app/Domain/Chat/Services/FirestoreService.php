<?php

namespace App\Domain\Chat\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use App\Domain\Chat\Models\Conversation;

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

    public function storeMessage($message)
    {
        $conversationId = (string) $message->conversation_id;
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return;
        }

        $participants = $conversation->participants()->pluck('user_id')->map(fn($id) => (string) $id)->toArray();

        $this->firestore->collection('conversations')
            ->document($conversationId)
            ->set([
                'conversation_id' => $conversationId,
                'conversation_name' => $conversation->name,
                'type' => $conversation->type,
                'created_at' => $conversation->created_at->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'participants' => $participants, 
            ], ['merge' => true]);

        $this->firestore->collection('conversations')
            ->document($conversationId)
            ->collection('messages')
            ->add([
                'user_id' => (string) $message->user_id,
                'message' => $message->message,
                'created_at' => now()->toDateTimeString(),
                // 'participants' => $participants, 
            ]);
    }

    public function addParticipants($conversationId, array $userIds)
    {
        $conversationRef = $this->firestore->collection('conversations')->document((string) $conversationId);
        $conversationData = $conversationRef->snapshot()->data();
        
        $existingParticipants = $conversationData['participants'] ?? [];
        $newParticipants = array_unique(array_merge($existingParticipants, $userIds));

        $conversationRef->set(['participants' => $newParticipants], ['merge' => true]);
    }
}
