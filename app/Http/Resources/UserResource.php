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
            'name' => $this->name,
            'avatar' => $this->avatar,
            'region' =>  new RegionResource($this->region),
            'city' =>  new CityResource($this->city),
            'neighborhood' =>  new NeighborhoodResource($this->neighborhood),
            'mobile_number_visibility' =>  $this->mobile_number_visibility,
            'birthdate_visibility' => $this->birthdate_visibility,
            'email_visibility' => $this->email_visibility,
        ];
    }
}
