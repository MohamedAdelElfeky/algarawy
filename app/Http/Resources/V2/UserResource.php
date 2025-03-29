<?php

namespace App\Http\Resources\v2;

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
            'last_name' => $this->last_name,
            'personal_title' => $this->personal_title,
            'family_name' =>  'القرعاوي',
            'email' => $this->getBooleanSetting('email_visibility') ? $this->email : null,
            'phone' => $this->getBooleanSetting('mobile_number_visibility') ? $this->phone : null,
            'occupation_category' => $this->occupation_category,
            'is_whatsapp' => $this->is_whatsapp,
            'birthdate' => $this->getBooleanSetting('birthdate_visibility') ? optional($this->details)->birthdate : null,
            'region' => optional(optional($this->details)->region)->name,
            'city' => optional(optional($this->details)->city)->name,
            'neighborhood' => optional(optional($this->details)->neighborhood)->name,
            'avatar' => $this->getImageByType('avatar'),
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
