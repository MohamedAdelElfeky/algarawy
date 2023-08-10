<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // public function getPosts()
    // {
    //     // Retrieve the posts associated with the authenticated user
    //     $posts = Post::where('user_id', Auth::id())->get();

    //     // Return the posts
    //     return response()->json([
    //         'posts' => $posts,
    //     ], 200);
    // }

    public function getMeetings()
    {
        // Retrieve the meetings associated with the authenticated user
        $meetings = Meeting::where('user_id', Auth::id())->get();

        // Return the meetings
        return response()->json([
            'meetings' => $meetings,
        ], 200);
    }

    public function getCourses()
    {
        // Retrieve the courses associated with the authenticated user
        $courses = Course::where('user_id', Auth::id())->get();

        // Return the courses
        return response()->json([
            'courses' => $courses,
        ], 200);
    }

    public function getUser()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Return the user information
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
                    $errors[] = "Invalid visibility value for {$field}.";
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
}
