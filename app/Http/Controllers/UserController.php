<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\ProjectResource;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
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
            'user' => $user,
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

    public function getDataUser()
    {
        $courses = CourseResource::collection(Course::where('user_id', Auth::id())->get());
        $projects = ProjectResource::collection(Project::where('user_id', Auth::id())->get());
        $meetings =   MeetingResource::collection(Meeting::where('user_id', Auth::id())->get());
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
        // dd($request->all());

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6', // Password is optional for update
            'birth_date' => 'nullable|date',
            'national_id' => 'nullable|string|size:11|unique:users,national_id,' . $user->id,
            'avatar' => 'nullable|file',
            'card_images' => 'nullable|array',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'national_card_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'national_card_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Update user profile data
        $user->fill($request->except('password'));

        // Handle password update if provided
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        if (request()->hasFile('avatar')) {
            $imageAvatar = request()->file('avatar');
            $file_name_avatar = time() . rand(0, 9999999999999) . '_avatar.' . $imageAvatar->getClientOriginalExtension();
            $imageAvatar->move(public_path('user/'), $file_name_avatar);
            $imagePathAvatar = "user/" . $file_name_avatar;
            $user->avatar = $imagePathAvatar;
        }
        // Handle file uploads
        if ($request->hasFile('national_card_image_front')) {
            $file = $request->file('national_card_image_front');
            $fileName = time() . '_front.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $user->national_card_image_front = $filePath;
        }

        if ($request->hasFile('national_card_image_back')) {
            $file = $request->file('national_card_image_back');
            $fileName = time() . '_back.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $user->national_card_image_back = $filePath;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function searchUser(Request $request)
    {
        $searchTerm = $request->input('search');
        $region_id = $request->input('region_id');
        $city_id = $request->input('city_id');
        $neighborhood_id = $request->input('neighborhood_id');

        $users = User::where(function ($query) use ($searchTerm) {
            $fields = ['first_name', 'last_name', 'name', 'phone'];
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

        return response()->json($users);
    }

    public function getNotificationsForUser()
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        return response()->json($notifications);
    }
}
