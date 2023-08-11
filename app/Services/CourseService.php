<?php

namespace App\Services;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\FilePdf;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'description' => 'required',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,mp4',
            'location' => 'nullable|string|location',
            'discount' => 'nullable',
            'link' => 'nullable|url',
            'images_and_videos.*' => 'file|mimes:jpeg,png,jpg,gif,mp4',
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
        // Handle images/videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_course.' . $image->getClientOriginalExtension();
                $image->move(public_path('course/images/'), $file_name);
                $imagePath = "course/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $course->images()->save($imageObject);
            }
        }
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $key => $item) {
                $pdf = $data['files'][$key];
                $pdfType = $pdf->getClientOriginalExtension();
                $mimeType = $pdf->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_course.' . $pdf->getClientOriginalExtension();
                $pdf->move(public_path('course/files/'), $file_name);
                $pdfPath = "course/files/" . $file_name;
                $pdfObject = new FilePdf([
                    'url' => $pdfPath,
                    'mime' => $mimeType,
                    'type' => $pdfType,
                ]);
                $course->pdfs()->save($pdfObject);
            }
        }
       

        return [
            'message' => 'تم إنشاء الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    public function updateCourse(Course $course, array $data)
    {
        if ($course->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'required',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,mp4',
            'location' => 'nullable|string|location',
            'discount' => 'nullable',
            'link' => 'nullable|url',
            'images_and_videos.*' => 'file|mimes:jpeg,png,jpg,gif,mp4',
            'deleted_images_and_videos' => 'nullable',
            'delete_files' => 'nullable',
        ]);

        $data['user_id'] = Auth::id();

        if ($validator->fails()) {
            return [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $deletedImagesAndVideos = $data['deleted_images_and_videos'] ?? [];
        foreach ($deletedImagesAndVideos as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                // Delete from storage
                Storage::delete($image->url);
                // Delete from database
                $image->delete();
            }
        }
        // Handle deleted files
        $deletedFiles = $data['delete_files'] ?? [];
        foreach ($deletedFiles as $fileId) {
            $filePdf = FilePdf::find($fileId);
            if ($filePdf) {
                // Delete from storage
                Storage::delete($filePdf->url);
                // Delete from database
                $filePdf->delete();
            }
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
        if ($course->user_id != Auth::id()) {
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
