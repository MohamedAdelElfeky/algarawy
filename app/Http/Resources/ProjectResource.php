<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'images_or_videos' => $this->images && $this->images->isNotEmpty()
                ? ImageResource::collection($this->images)
                : [asset('default.png')],
            'files' => $this->pdfs ? FilePdfResource::collection($this->pdfs) : null,
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
