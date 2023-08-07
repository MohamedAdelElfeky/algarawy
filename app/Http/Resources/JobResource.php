<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'qualifications' => $this->qualifications,
            'location' => $this->location,
            'contact_information' => $this->contact_information,
            'photo' => $this->photo,
            'company_name' => $this->company_name,
            'company_location' => $this->company_location,
            'company_type' => $this->company_type,
            'company_link' => $this->company_link,
            'company_logo' => $this->company_logo,
            'job_type' => $this->job_type,
            'is_training' => $this->is_training,
            'is_full_time' => $this->is_full_time,
            'price' => $this->price,
            'job_status' => $this->job_status,
            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files_pdf' => $this->files_pdf ? FilePdfResource::collection($this->pdfs) : null,
            'user' => $this->user,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
