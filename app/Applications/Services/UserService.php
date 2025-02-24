<?php
namespace App\Application\Services;

use App\Infrastructure\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->createUser($data);
        $this->userRepository->createUserDetail($user, $data);

        return $user;
    }

    public function loginUser(array $credentials)
    {
        $user = $this->userRepository->findByNationalId($credentials['national_id']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }
}
