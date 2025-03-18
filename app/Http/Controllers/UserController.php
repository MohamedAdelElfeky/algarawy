<?php

namespace App\Http\Controllers;

use App\Domain\Models\Course;
use App\Domain\Models\Meeting;
use App\Domain\Models\Project;
use App\Domain\Models\Setting;
use App\Domain\Models\support;
use App\Domain\Models\UserDetail;
use App\Domain\Models\UserSetting;
use App\Domain\Services\PaginationService;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FileHandlerService;
use App\Shared\Traits\HandlesSingleImageUpload;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HandlesSingleImageUpload;

    public function __construct(private PaginationService $paginationService)
    {
        $this->middleware('auth:sanctum');
    }

    public function getMeetings()
    {
        $meetings = Meeting::where('user_id', Auth::id())->get();
        return response()->json([
            'meetings' => $meetings,
        ], 200);
    }

    public function getCourses()
    {
        $courses = Course::where('user_id', Auth::id())->get();
        return response()->json([
            'courses' => $courses,
        ], 200);
    }

    public function getUser()
    {
        $user = Auth::user()->load('details.region', 'details.city', 'details.neighborhood', 'details.images');
        return response()->json([
            'user' =>  new UserResource($user),
        ], 200);
    }

    public function getDataUser($userId)
    {

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $showNoComplaintedPosts = $user->show_no_complainted_posts == 1;

        // Apply filters to each query
        $coursesQuery = Course::where('user_id', $userId);
        $projectsQuery = Project::where('user_id', $userId);
        $meetingsQuery = Meeting::where('user_id', $userId);

        $this->applyComplaintFilter($coursesQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($projectsQuery, $showNoComplaintedPosts, $user);
        $this->applyComplaintFilter($meetingsQuery, $showNoComplaintedPosts, $user);

        $courses = CourseResource::collection($coursesQuery->get());
        $projects = ProjectResource::collection($projectsQuery->get());
        $meetings = MeetingResource::collection($meetingsQuery->get());

        $oneRowArray = [
            'Course' => $courses,
            'Project' => $projects,
            'Meeting' => $meetings,
        ];
        $result = [
            "date" => [],
        ];
        foreach ($oneRowArray as $type => $data) {
            $formattedData = [
                "type" => $type,
                "data" => $data,
            ];

            $result["date"][] = $formattedData;
        }
        return $result;
    }

    /**
     * Apply the complaint filter to the query based on user preferences.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $showNoComplaintedPosts
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyComplaintFilter($query, $showNoComplaintedPosts, $user)
    {
        if ($showNoComplaintedPosts) {
            $query->whereDoesntHave('complaints', function ($query) use ($user) {
                $query->where('user_id', '=', $user->id); // Exclude user complaints
            });
        } else {
            $query; //->has('complaints');
        }

        return $query;
    }

    public function updateProfile(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'is_avatar_deleted' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        if ($request->input('is_avatar_deleted')) {
            if ($user->avatar) {
                $oldAvatarPath = public_path($user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
                $user->avatar = null;
            }
        }
        if ($request->hasFile('avatar')) {
            $imageAvatar = $request->file('avatar');
            $file_name_avatar = time() . '_' . $imageAvatar->getClientOriginalName();
            $imageAvatar->move(public_path('user/'), $file_name_avatar);
            $imagePathAvatar = "user/" . $file_name_avatar;
            $user->avatar = $imagePathAvatar;
        }
        $user->update([
            'first_name' => $request->input('first_name', $user->first_name),
            'last_name' => $request->input('last_name', $user->last_name),
            'email' => $request->input('email', $user->email),
            'phone' => $request->input('phone', $user->phone),
        ]);

        return response()->json(new UserResource($user));
    }

    public function searchUser(Request $request)
    {
        $searchTerm = $request->input('search');
        $region_id = $request->input('region_id');
        $city_id = $request->input('city_id');
        $neighborhood_id = $request->input('neighborhood_id');

        $users = User::where(function ($query) use ($searchTerm) {
            $fields = ['first_name', 'last_name', 'phone'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })
            // ->when($region_id, function ($query) use ($region_id) {
            //     return $query->orWhere('region_id', $region_id);
            // })
            // ->when($city_id, function ($query) use ($city_id) {
            //     return $query->orWhere('city_id', $city_id);
            // })
            // ->when($neighborhood_id, function ($query) use ($neighborhood_id) {
            //     return $query->orWhere('neighborhood_id', $neighborhood_id);
            // })
            ->get();

        return response()->json(UserResource::collection($users));
    }

    public function getNotificationsForUser(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $notificationsResource = NotificationResource::collection($notifications);
        $paginationData = $this->paginationService->getPaginationData($notifications);

        return [
            'data' => $notificationsResource,
            'metadata' => $paginationData,
        ];
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }
        $user = Auth::user();

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json(['error' => 'كلمة المرور القديمة غير متطابقة'], 403);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['message' => 'تم تحديث كلمة السر بنجاح']);
    }

    public function numberSupport()
    {
        $supportRecord = support::first();
        $number = $supportRecord ? $supportRecord->number : null;
        return response()->json(['number' =>  $number]);
    }

    public function getAllUsers()
    {
        $users = User::all();
        return view('pages.dashboards.users.index', compact('users'));
    }


    public function toggleUser($id)
    {
        $user = User::findOrFail($id);

        $setting = Setting::where('key', 'registration_confirmed')->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
        }

        $userSetting = $user->userSettings()->where('setting_id', $setting->id)->first();

        if ($userSetting) {
            $newValue = !$userSetting->value;

            $userSetting->update(['value' => $newValue]);
        } else {
            $user->userSettings()->create([
                'setting_id' => $setting->id,
                'value' => true,
            ]);
        }

        return response()->json(['success' => true]);
    }




    public function userActive()
    {
        $registrationConfirmedSetting = Setting::where('key', 'registration_confirmed')->first();

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
            ->whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
                $query->where('setting_id', $registrationConfirmedSetting->id)
                    ->where('value', 1);
            })
            ->with(['details', 'roles', 'userSettings.setting'])
            ->paginate(25);

        return view('pages.dashboards.users.user_active', compact('users'));
    }

    public function userNotActive()
    {
        $registrationConfirmedSetting = Setting::where('key', 'registration_confirmed')->first();

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
            ->whereHas('userSettings', function ($query) use ($registrationConfirmedSetting) {
                $query->where('setting_id', $registrationConfirmedSetting->id)
                    ->where('value', 0);
            })
            ->with(['details', 'roles', 'userSettings.setting'])
            ->paginate(25);

        return view('pages.dashboards.users.user_not_active', compact('users'));
    }

    public function admin()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->with(['details', 'roles'])
            ->paginate(25);
        return view('pages.dashboards.admin.index', compact('users'));
    }


    public function addUser(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|confirmed',
            'birth_date' => 'required|date',
            'national_id' => 'required|unique:users',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        $imageFields = ['avatar', 'national_card_image_front', 'national_card_image_back'];
        $user = User::updateOrCreate(
            ['email' => $validatedData['email']],
            [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'national_id' => $validatedData['national_id'],
            ]
        );

        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birthdate' => $validatedData['birth_date'],
            ]
        );
        foreach ($imageFields as $field) {
            $this->uploadSingleImage($request, $userDetail, 'users', 'user', 'image', $field);
        }
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($adminRole);

        $settings = [
            'mobile_number_visibility' => true,
            'birthdate_visibility' => true,
            'email_visibility' => true,
            'registration_confirmed' => true,
            'show_no_complaints_posts' => true,
        ];

        foreach ($settings as $settingName => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_id' => $this->getSettingIdByName($settingName)],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'تمت إضافة المسؤول بنجاح']);
    }

    private function getSettingIdByName($name)
    {
        return Setting::where('key', $name)->value('id');
    }

    public function changePasswordByAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
    }

    public function makeAdmin($userId)
    {
        $user = User::findOrFail($userId);

        // Ensure the "admin" role exists, if not create it
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Assign the role to the user
        $user->assignRole($adminRole);

        return redirect()->back()->with('success', 'User has been assigned as Admin.');
    }
}
