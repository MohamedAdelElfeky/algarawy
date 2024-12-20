<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => $this->id,
            'avatar' => $this->avatar,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date ? date('Y-m-d', strtotime($this->birth_date)) : null,
            'region' =>  new RegionResource($this->region),
            'city' =>  new CityResource($this->city),
            'neighborhood' =>  new NeighborhoodResource($this->neighborhood),
            'mobile_number_visibility' =>  $this->mobile_number_visibility,
            'birthdate_visibility' => $this->birthdate_visibility,
            'email_visibility' => $this->email_visibility,
            'show_no_complainted_posts' => $this->show_no_complainted_posts,
        ];
    }
}
