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
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name ?? '-',
            'personal_title' => $this->personal_title,
            'family_name' =>  'القرعاوي',
            'email' => $this->email,
            'phone' => $this->phone,
            'occupation_category' => $this->occupation_category,
            'is_whatsapp' => $this->is_whatsapp,

            'birthdate' => optional($this->details)->birthdate  ? optional($this->details)->birthdate : null,
            'region' => optional(optional($this->details)->region)->name,
            'city' => optional(optional($this->details)->city)->name,
            'neighborhood' => optional(optional($this->details)->neighborhood)->name,


            'mobile_number_visibility' => $this->getBooleanSetting('mobile_number_visibility'),
            'birthdate_visibility' => $this->getBooleanSetting('birthdate_visibility'),
            'email_visibility' => $this->getBooleanSetting('email_visibility'),
            'show_no_complaints_posts' => $this->getBooleanSetting('show_no_complaints_posts'),
            'registration_confirmed' => $this->getBooleanSetting('registration_confirmed'),

            'avatar' => $this->getImageByType('avatar'),
            'national_card_image_front' => $this->getImageByType('national_card_image_front'),
            'national_card_image_back' => $this->getImageByType('national_card_image_back'),
            'token_info'=>TokenInfoResource::make($this->whenLoaded('token_info')),
            'card_images' => $this->getImageByType('card_images'),
        ];
    }

    protected function getBooleanSetting(string $key): bool
    {
        $value = $this->getSettingValue($key);
        return $value === true || $value === 'true' || $value === 1 || $value === '1';
    }

    protected function getImageByType(string $type): ?string
    {
        return $this->whenLoaded('details', function () use ($type) {
            return optional($this->details->images->where('type', $type)->first())->url;
        });
    }
}
