<?php

namespace App\Domain\Services;

use App\Domain\Models\Course;
use App\Domain\Models\Meeting;
use App\Domain\Models\Project;
use App\Filters\NoComplaintsFilter;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserDataService
{
    use HandlesFileDeletion, HandlesSingleImageUpload;

    public function __construct(private PaginationService $paginationService) {}

    public function getMeetings($userId)
    {
        return Meeting::where('user_id', $userId)->get();
    }

    public function getCourses($userId)
    {
        return Course::where('user_id', $userId)->get();
    }

    public function getUserData($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found', 'status' => 404];
        }

        $queries = [
            'courses' => Course::query()->where('user_id', $userId),
            'projects' => Project::query()->where('user_id', $userId),
            'meetings' => Meeting::query()->where('user_id', $userId),
        ];

        foreach ($queries as $key => $query) {
            $queries[$key] = app(Pipeline::class)
                ->send($query)
                ->through([NoComplaintsFilter::class])
                ->thenReturn();
        }

        return [
            "date" => [
                ["type" => "Course", "data" => CourseResource::collection($queries['courses']->get())],
                ["type" => "Project", "data" => ProjectResource::collection($queries['projects']->get())],
                ["type" => "Meeting", "data" => MeetingResource::collection($queries['meetings']->get())],
            ]
        ];
    }


    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        $this->uploadSingleImage($request, $user, 'user', 'avatar', 'avatar', 'images');
        $user->update($request->only(['first_name', 'middle_name', 'personal_title', 'last_name', 'email']));

        return $user;
    }

    public function getNotificationsForUser($perPage, $page)
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => NotificationResource::collection($notifications),
            'metadata' => $this->paginationService->getPaginationData($notifications),
        ];
    }

    public function changePassword($data)
    {
        $validator = Validator::make($data, [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors(), 'status' => 403];
        }

        $user = Auth::user();

        if (!Hash::check($data['old_password'], $user->password)) {
            return ['error' => 'كلمة المرور القديمة غير متطابقة', 'status' => 403];
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return ['message' => 'تم تحديث كلمة السر بنجاح', 'status' => 200];
    }
}
