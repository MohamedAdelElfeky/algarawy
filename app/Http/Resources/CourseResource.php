<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            // 'name' => $this->name,
            'description' => $this->description,
            'files' => $this->files,
            'location' => $this->location,
            'discount' => $this->discount,
            'link' => $this->link,
            'images_and_videos' => $this->images_and_videos,
            'user' => $this->user,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
        
    }
}
