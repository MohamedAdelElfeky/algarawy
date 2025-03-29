<?php

namespace App\Domain\Services;

use App\Models\User;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

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
            'user' => new UserResource($user),
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
            $userRole = Role::firstOrCreate(['name' => 'user']);
            $user->assignRole($userRole);

            $settings = [
                'mobile_number_visibility' => true,
                'birthdate_visibility' => true,
                'email_visibility' => true,
                'registration_confirmed' => false,
                'show_no_complaints_posts' => true,
            ];

            foreach ($settings as $key => $value) {
                UserSetting::updateOrCreate(
                    ['user_id' => $user->id, 'setting_id' => $this->getSettingIdByName($key)],
                    ['value' => $value]
                );
            }
            return [
                'message' => 'تم التسجيل بنجاح',
                'user' => new UserResource($user),
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
                'middle_name' => $data['middle_name'] ?? null,
                'personal_title' => $data['personal_title'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'national_id' => $data['national_id'],
                'occupation_category' => $data['occupation_category'] ?? null,
                'is_whatsapp' => $data['is_whatsapp'] ?? false,
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
        $imageFields = ['avatar', 'national_card_image_front', 'national_card_image_back', 'card_images'];
        foreach ($imageFields as $field) {
            $this->uploadSingleImage($request, $userDetail, 'users', 'user',  $field, $field);
        }
    }
    private function getSettingIdByName($name)
    {
        return \App\Domain\Models\Setting::where('key', $name)->value('id');
    }
}
