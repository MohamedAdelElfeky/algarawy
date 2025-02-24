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

            'settings' => [
//                'mobile_number_visibility' => (bool) $this->getSettingValue('mobile_number_visibility', 1),
//                'birthdate_visibility' => (bool) $this->getSettingValue('birthdate_visibility', 1),
//                'email_visibility' => (bool) $this->getSettingValue('email_visibility', 1),
//                'show_no_complainted_posts' => (bool) $this->getSettingValue('show_no_complainted_posts', 0),
            ],

            'details' => $this->details ? new UserDetailResource($this->details) : null,
        ];
    }
}
