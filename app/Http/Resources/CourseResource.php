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
            'description' => $this->description,
            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files_pdf' => $this->files_pdf ? FilePdfResource::collection($this->pdfs) : null,
            'location' => $this->location,
            'discount' => $this->discount,
            'link' => $this->link,
            'user' => $this->user,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
