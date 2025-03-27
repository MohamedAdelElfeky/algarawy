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
            'participants' => $this->participants->pluck('user_id')->toArray(),
            'messages' => ChatMessageResource::collection($this->whenLoaded('messages')),
            'last_message' => new ChatMessageResource($this->messages()->latest()->first()),
            'last_created_at_message' => optional($this->messages()->latest('created_at')->first())->created_at?->format('Y-m-d H:i:s'),
            'last_updated_at_message' => optional($this->messages()->latest('updated_at')->first())->updated_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
