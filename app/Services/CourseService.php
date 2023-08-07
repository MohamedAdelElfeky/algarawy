<?php

namespace App\Services;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseFile;
use App\Models\CourseImageVideo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function createCourse(array $data)
    {
        $validator = Validator::make($data, [
            // 'name' => 'required',
            'description' => 'required',
            'files' => 'required',
            'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'required|string|location',
            'discount' => 'nullable',
            'link' => 'nullable|url',
            'images_and_videos' => 'required',
            'images_and_videos.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        // return $data;
        $course = Course::create($data);

        if (request()->hasFile('files')) {
            dd(sizeof(request()->file('files')));
            foreach (request()->file('files') as $file) {
                dd($file);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                // Move the uploaded file to the desired directory
                $file->move('public/upload/courses', $filename);

                // Save the file path in the database for later retrieval if needed
                CourseFile::create([
                    'course_id' => $course->id,
                    'file_path' => 'upload/courses/' . $filename, // Save the file path relative to the 'public' disk
                ]);
            }
        }

        // Handle 'images_and_videos' array
        if (isset($data['images_and_videos'])) {
            foreach ($data['images_and_videos'] as $imageOrVideoFile) {
                $extension = $imageOrVideoFile->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                // Move the uploaded file to the desired directory
                $imageOrVideoFile->move('public/upload/courses', $filename);

                // Save the file path in the database for later retrieval if needed
                CourseImageVideo::create([
                    'course_id' => $course->id,
                    'file_path' => 'upload/courses/' . $filename, // Save the file path relative to the 'public' disk
                ]);
            }
        }

        return [
            'message' => 'تم إنشاء الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    public function updateCourse(Course $course, array $data)
    {
        if (($course->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            // 'name' => 'sometimes|required',
            'description' => 'sometimes|required',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'required|string|google_maps_location',
            'discount' => 'nullable',
            'link' => 'nullable|url',
            'images_and_videos' => 'nullable|array',
            'images_and_videos.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',

        ]);
        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $course->update($data);

        return [
            'data' => new CourseResource($course),
        ];
    }

    public function getAllCourses($perPage = 10, $page = 1)
    {
        $courses = Course::paginate($perPage, ['*'], 'page', $page);
        $courseResource = CourseResource::collection($courses);
        $paginationData = $this->paginationService->getPaginationData($courses);

        return [
            'data' => $courseResource,
            'metadata' => $paginationData,
        ];
        return;
    }

    public function getCourseById($id)
    {
        $course = Course::find($id);
        if (!$course) {
            abort(404, 'الدورة غير موجودة');
        }
        return $course;
    }

    public function deleteCourse(string $id)
    {
        $course = Course::findOrFail($id);

        if (!$course) {
            return ['message' => 'الدورة غير موجودة'];
        }
        if (($course->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 200);
        }
        $course->delete();

        return ['message' => 'تم حذف الدورة بنجاح'];
    }
    public function searchCourse($searchTerm)
    {
        $courses = Course::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return CourseResource::collection($courses);
    }
}
