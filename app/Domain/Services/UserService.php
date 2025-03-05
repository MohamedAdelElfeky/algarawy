<?php

namespace App\Domain\Services;

use App\Domain\Aggregates\UserAggregate;
use App\Models\User;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data)
    {
        $user = $this->userRepository->createUser($data);
        if (isset($data['birth_date'])) {
            $user->details()->create([
                'birth_date' => $data['birth_date'],
                'region_id' => $data['region_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'neighborhood_id' => $data['neighborhood_id'] ?? null,
            ]);
        }

        return new UserAggregate($user);
    }

    public function updateUser(User $user, array $data)
    {
        $this->userRepository->updateUser($user, $data);

        if ($user->details) {
            $user->details()->update([
                'birth_date' => $data['birth_date'] ?? $user->details->birth_date,
                'region_id' => $data['region_id'] ?? $user->details->region_id,
                'city_id' => $data['city_id'] ?? $user->details->city_id,
                'neighborhood_id' => $data['neighborhood_id'] ?? $user->details->neighborhood_id,
            ]);
        } else {
            $user->details()->create([
                'birth_date' => $data['birth_date'] ?? null,
                'region_id' => $data['region_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'neighborhood_id' => $data['neighborhood_id'] ?? null,
            ]);
        }

        return new UserAggregate($user);
    }

    public function registerUser(array $data)
    {

        $data['password'] = Hash::make($data['password']);

        // Create User
        $user = $this->userRepository->createUser([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'national_id' => $data['national_id']
        ]);

        // Save User Details
        if (isset($data['location']) || isset($data['birthdate']) || isset($data['region_id']) || isset($data['city_id']) || isset($data['neighborhood_id'])) {
            $userDetail = $user->details()->create([
                'location' => $data['location'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'region_id' => $data['region_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'neighborhood_id' => $data['neighborhood_id'] ?? null,
            ]);
            $this->saveUserImages($userDetail, $data);
        }
        // Save Images

        return $user;
    }

    /**
     * Handle Image Upload and Save in Images Table
     */
    private function saveUserImages($userDetail, $data)
    {
        $imageFields = [
            'avatar' => 'avatar',
            'national_card_image_front' => 'national_card_image_front',
            'national_card_image_back' => 'national_card_image_back',
            'card_image' => 'card_image'
        ];

        foreach ($imageFields as $field => $typeName) {
            if (isset($data[$field])) {
                $imagePath = $this->handleImageUpload($data[$field]);

                $userDetail->images()->create([
                    'url' => $imagePath,
                    'type_name' => $typeName
                ]);
            }
        }
    }


    /**
     * Upload Image and Return Path
     */
    private function handleImageUpload($image)
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('users', $filename, 'public');
        return 'storage/' . $path;
    }

    //    public function findUserByNationalId(string $nationalId)
    //    {
    //        return User::where('national_id', $nationalId)->first();
    //    }

    public function loginUser(array $credentials)
    {
        $user = $this->userRepository->findByNationalId($credentials['national_id']);
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }
}
