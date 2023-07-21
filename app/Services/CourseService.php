<?php

namespace App\Services;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseService
{
    public function createCourse(array $data)
    {
        $validator = Validator::make($data, [
            'description' => 'required',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'nullable',
            'discount' => 'nullable',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $course = Course::create($data);

        return [
            'success' => true,
            'message' => 'Course created successfully',
            'data' => new CourseResource($course),
        ];
    }

    public function updateCourse(Course $course, array $data)
    {
        $validator = Validator::make($data, [
            'description' => 'required',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'nullable',
            'discount' => 'nullable',
            'user_id' => 'required|exists:users,id',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $course->update($data);

        return [
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => new CourseResource($course),
        ];
    }  

    public function getAllCourses()
    {
        $course = Course::all();
        return CourseResource::collection($course);

    }

    public function getCourseById($id)
    {
        $course = Course::find($id);
        if (!$course) {
            abort(404, 'Course not found');
        }
        return $course;
    }

    public function deleteCourse(string $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
