<?php

namespace App\Domain\Services;

use App\Models\User;
use App\Domain\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    use HandlesSingleImageUpload;

    public function login(array $credentials)
    {
        $user = User::where('national_id', $credentials['national_id'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'message' => 'بيانات تسجيل الدخول غير صحيحة', 
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        return [
            'user' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
            'status' => Response::HTTP_CREATED
        ];
    }

    public function register(array $validatedData, $request)
    {
        try {
            $user = $this->createOrUpdateUser($validatedData);
            $userDetail = $this->createOrUpdateUserDetail($user, $validatedData);
            $this->handleImageUploads($request, $userDetail);

            return [
                'message' => 'تم التسجيل بنجاح',
                'user' => $user,
                'status' => Response::HTTP_CREATED
            ];
        } catch (ValidationException $e) {
            return [
                'error' => 'خطأ في البيانات المدخلة',
                'details' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'حدث خطأ أثناء التسجيل',
                'details' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }

    private function createOrUpdateUser(array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'national_id' => $data['national_id'],
            ]
        );
    }

    private function createOrUpdateUserDetail(User $user, array $data): UserDetail
    {
        return UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birthdate' => $data['birth_date'] ?? null,
                'region_id' => $data['region_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'neighborhood_id' => $data['neighborhood_id'] ?? null,
            ]
        );
    }

    private function handleImageUploads($request, UserDetail $userDetail): void
    {
        $imageFields = ['avatar', 'national_card_image_front', 'national_card_image_back'];

        foreach ($imageFields as $field) {
            $this->uploadSingleImage($request, $userDetail, 'users', 'user', 'image', $field);
        }
    }
}
