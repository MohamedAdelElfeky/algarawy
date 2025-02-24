<?php

namespace App\Domain\Aggregates;

use App\Domain\Models\User;

class UserAggregate
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toArray()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->first_name . ' ' . $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'details' => $this->user->details ? [
                'birth_date' => $this->user->details->birth_date,
                'region_id' => $this->user->details->region_id,
                'city_id' => $this->user->details->city_id,
                'neighborhood_id' => $this->user->details->neighborhood_id,
            ] : null,
        ];
    }
}
