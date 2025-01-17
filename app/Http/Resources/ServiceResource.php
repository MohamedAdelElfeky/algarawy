<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends JsonResource
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
            'description' => $this->description,
            'images_or_videos' => $this->images ? ImageResource::collection($this->images) :  [asset('default.png')],
            'location' => $this->location,
            'user' => new UserResource($this->user),
            
            'favorite' => $this->favorites->where('user_id', Auth::id())->where('favoritable_id', $this->id)->count() > 0,
            'count_favorite' => $this->favorites->where('favoritable_id', $this->id)->count() > 0,

            'like' => $this->likes->where('user_id', Auth::id())->where('likable_id', $this->id)->count() > 0,
            'count_like' => $this->likes->where('likable_id', $this->id)->count(),

            'complaint' => $this->complaints->where('user_id', Auth::id())->where('complaintable_id', $this->id)->count() > 0,
            'count_complaint' => $this->complaints->where('complaintable_id', $this->id)->count(),
            'status' => $this->status,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
