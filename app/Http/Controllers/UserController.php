<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
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
        $user = Auth::user();
        return response()->json([
            'user' =>  new UserResource($user),
        ], 200);
    }

    public function toggleVisibility(Request $request)
    {
        $user = auth()->user();

        $fields = ['mobile_number', 'birthdate', 'email'];

        $errors = [];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $visibility = $request->input($field);

                if (!is_bool($visibility)) {
                    $errors[] = "قيمة رؤية غير صالحة لـ {$field}.";
                } else {
                    $user->{$field . '_visibility'} = $visibility;
                }
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }

        $user->save();

        return response()->json(['message' => 'تم تحديث إعدادات الرؤية.']);
    }

    public function getDataUser($userId)
    {
        $courses = CourseResource::collection(Course::where('user_id', $userId)->get());
        $projects = ProjectResource::collection(Project::where('user_id', $userId)->get());
        $meetings =   MeetingResource::collection(Meeting::where('user_id', $userId)->get());
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

    public function updateProfile(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_avatar_deleted' => 'nullable|boolean', // New field to handle avatar deletion
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
            ->when($region_id, function ($query) use ($region_id) {
                return $query->orWhere('region_id', $region_id);
            })
            ->when($city_id, function ($query) use ($city_id) {
                return $query->orWhere('city_id', $city_id);
            })
            ->when($neighborhood_id, function ($query) use ($neighborhood_id) {
                return $query->orWhere('neighborhood_id', $neighborhood_id);
            })
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
        $notificationsRe = $user->notifications()
            ->orderBy('created_at', 'desc')->get();
        $paginationData = $this->paginationService->getPaginationData($notifications);

        return [
            'data' => $notificationsRe,
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
        return response()->json(['number' => '+96614584684']);
    }

    public function getAllUsers()
    {
        $users = User::all();
        return view('pages.dashboards.users.index', compact('users'));
    }
    public function toggleUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['registration_confirmed' => !$user->active]);

        return response()->json(['success' => true]);
    }
}
