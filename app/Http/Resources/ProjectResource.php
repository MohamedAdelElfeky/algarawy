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
            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files' => $this->files_pdf ? FilePdfResource::collection($this->pdfs) : null,
            'location' => $this->location,
            'user' => $this->user_id,
            'favorite' => $this->favorites->where('user_id', Auth::id())->where('favoritable_id', $this->id)->count() > 0,
            'like' => $this->likes->where('user_id', Auth::id())->where('likable_id', $this->id)->count() > 0,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
