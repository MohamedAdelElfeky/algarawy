<?php

namespace App\Services;

use App\Models\Course;
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
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        // Handle file upload if needed

        $course = Course::create($data);

        return [
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course,
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

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        // Handle file upload if needed

        $course->update($data);

        return [
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course,
        ];
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();

        return [
            'success' => true,
            'message' => 'Course deleted successfully',
        ];
    }
}
