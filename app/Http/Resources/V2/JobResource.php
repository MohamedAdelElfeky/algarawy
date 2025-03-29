<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\CityResource;
use App\Http\Resources\FilePdfResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\NeighborhoodResource;
use App\Http\Resources\RegionResource;
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

            'company_name' => optional($this->JobCompanies)->name,
            'company_description' =>  optional($this->JobCompanies)->description,
            'company_location' => optional($this->JobCompanies)->location,
            'company_type' => optional($this->JobCompanies)->type,
            'company_link' => optional($this->JobCompanies)->link,
            'company_logo' => $this->company_logo
                ?  $this->company_logo
                : 'default.png',

            'company_region' =>  new RegionResource(optional($this->JobCompanies)->region),
            'company_city' =>  new CityResource(optional($this->JobCompanies)->city),
            'company_neighborhood' =>  new NeighborhoodResource(optional($this->JobCompanies)->neighborhood),

            'job_type' => $this->job_type,
            'job_duration' => $this->job_duration,
            'is_training' => $this->is_training,
            'price' => $this->price,
            'job_status' => $this->job_status,

            'images_or_videos' => $this->images ? ImageResource::collection($this->images) : null,
            'files' => $this->pdfs ? FilePdfResource::collection($this->pdfs) : null,

            'user' => new UserResource($this->user),

            'favorite' => $this->favorites->where('user_id', Auth::id())->where('favoritable_id', $this->id)->count() > 0,
            'count_favorite' => $this->favorites->where('favoritable_id', $this->id)->count() > 0,

            'like' => $this->likes->where('user_id', Auth::id())->where('likable_id', $this->id)->count() > 0,
            'count_like' => $this->likes->where('likable_id', $this->id)->count(),

            'complaint' => $this->complaints->where('user_id', Auth::id())->where('complaintable_id', $this->id)->count() > 0,
            'count_complaint' => $this->complaints->where('complaintable_id', $this->id)->count(),

            'count_apply_job' => $this->count_of_applications, // JobApplication::where('job_id',$this->id)->count(),
            'status' => $this->status,
            
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),

        ];
    }
}
