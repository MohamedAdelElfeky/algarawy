<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\FilePdfResource;
use App\Http\Resources\ImageResource;
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
            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files' => $this->pdfs ? FilePdfResource::collection($this->pdfs) : null,
            'location' => $this->location,
            'user' => new UserResource($this->user),

            'favorite' => $this->favorites->where('user_id', Auth::id())->where('favoritable_id', $this->id)->count() > 0,
            'count_favorite' => $this->favorites->where('favoritable_id', $this->id)->count() > 0,

            'like' => $this->likes->where('user_id', Auth::id())->where('likable_id', $this->id)->count() > 0,
            'count_like' => $this->likes->where('likable_id', $this->id)->count(),

            'complaint' => $this->complaints->where('user_id', Auth::id())->where('complaintable_id', $this->id)->count() > 0,
            'count_complaint' => $this->complaints->where('complaintable_id', $this->id)->count(),
            // 'approval' => $this->approval,
            // 'status' => $this->approval,

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),

        ];
    }
}
