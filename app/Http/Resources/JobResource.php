<?php

namespace App\Http\Resources;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            'description' => $this->description,
            'title' => $this->title,

            'region' =>  new RegionResource($this->region),
            'city' =>  new CityResource($this->city),
            'neighborhood' =>  new NeighborhoodResource($this->neighborhood),

            'company_name' => $this->company_name,
            'company_description' => $this->company_description,
            'company_location' => $this->company_location,
            'company_type' => $this->company_type,
            'company_link' => $this->company_link,
            'company_logo' => $this->company_logo,

            'company_region' =>  new RegionResource($this->companyRegion),
            'company_city' =>  new CityResource($this->companyCity),
            'company_neighborhood' =>  new NeighborhoodResource($this->companyNeighborhood),

            'job_type' => $this->job_type,
            'job_duration' => $this->job_duration,
            'is_training' => $this->is_training,
            'price' => $this->price,
            'job_status' => $this->job_status,

            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files' => $this->pdfs ? FilePdfResource::collection($this->pdfs) : null,

            'user' => new UserResource($this->user),

            'favorite' => $this->favorites->where('user_id', Auth::id())->where('favoritable_id', $this->id)->count() > 0,
            'like' => $this->likes->where('user_id', Auth::id())->where('likable_id', $this->id)->count() > 0,

            'count_apply_job' => JobApplication::where('job_id',$this->id)->count(),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
