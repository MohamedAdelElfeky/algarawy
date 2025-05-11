<?php

namespace App\Domain\Services\V2;

use App\Domain\Entities\OTP;
use App\Domain\Entities\PhoneNumber;
use App\Domain\Models\PendingUser;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserDevice;
use App\Domain\Models\UserSetting;
use App\Http\Resources\V2\UserResource;
use App\Infrastructure\Services\TwilioService;
use App\Models\User;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class AuthService
{
    use HandlesSingleImageUpload;

    public function __construct(private TwilioService $twilioService) {}

    public function tempRegister(array $data): PendingUser
    {
        $pending = PendingUser::where('phone', $data['phone'])->first();

        if ($pending && !$pending->is_verified) {
            $pending->update($data);
        } else {
            $pending = PendingUser::create($data);
        }

        $this->twilioService->sendOtp(new PhoneNumber($pending->phone));

        return $pending;
    }

    public function verifyOtp(array $data, $request): array
    {
        $pending = PendingUser::where('phone', $data['phone'])->first();
        if (!$pending) {
            return [
                'error' => 'رقم الهاتف غير موجود أو تم التحقق منه مسبقًا.',
                'status' => Response::HTTP_NOT_FOUND
            ];
        }
        $phone = new PhoneNumber($request->phone);
            $otp = new OTP($request->otp);
        if (!$this->twilioService->verifyOtp($phone, $otp)) {
            return [
                'error' => 'رمز التحقق غير صحيح.',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }

        try {
            DB::transaction(function () use ($pending, $request, &$user) {
                $validatedData = $pending->toArray();
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

                UserDevice::updateOrCreate(
                    ['user_id' => $user->id, 'device_id' => $request->device_id],
                    [
                        'notification_token' => $request->notification_token,
                        'auth_token' => null,
                    ]
                );
                $pending->delete();
            });

            return [
                'message' => 'تم التحقق وإنشاء الحساب بنجاح',
                'user' => new UserResource($user),
                'status' => Response::HTTP_OK
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'حدث خطأ أثناء إنشاء الحساب',
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
