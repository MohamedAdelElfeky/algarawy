<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'participants' => ConversationParticipantResource::collection($this->whenLoaded('participants')),
            'messages' => ChatMessageResource::collection($this->whenLoaded('messages')),
            'last_message' => new ChatMessageResource($this->messages()->latest()->first()), 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
