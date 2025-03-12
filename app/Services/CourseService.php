<?php

namespace App\Services;

use App\Domain\Models\Course;
use App\Domain\Models\FilePdf;
use App\Domain\Models\Image;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseService
{


    public function __construct(private PaginationService $paginationService, private FileHandlerService $fileHandler) {}


    public function createCourse(CourseRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        $course = Course::create($validatedData);
        $course->Approval()->create([
            'status' => 'pending'
        ]);
        $course->visibility()->create([
            'status' => 'private'
        ]);
        $this->fileHandler->attachImages($request, $course, 'courses/images', 'course_');
        $this->fileHandler->attachPdfs($request, $course, 'courses/pdf', 'pdf_');

        return [
            'message' => 'تم إنشاء الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    public function updateCourse(Course $course, CourseRequest $request)
    {
        if (!$course->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 403);
        }

        $validatedData = $request->validated();
        if ($request->filled('deleted_images_and_videos')) {
            $this->fileHandler->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        if ($request->filled('deleted_files')) {
            $this->fileHandler->deleteFiles($request->deleted_files, 'pdf');
        }
        $course->update($validatedData);
        $this->fileHandler->attachImages($request, $course, 'courses/images', 'course_');
        $this->fileHandler->attachPdfs($request, $course, 'courses/pdf', 'pdf_');

        return [
            'message' => 'تم تحديث الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    public function getCourses($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();

        $courseQuery = Course::query()->approvalStatus('approved')->orderByDesc('created_at');

        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $courseQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $courseQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $courseQuery->visibilityStatus();
        }

        $courses = $courseQuery->paginate($perPage, ['*'], 'page', $page);


        return [
            'data' => CourseResource::collection($courses),
            'metadata' => $this->paginationService->getPaginationData($courses),
        ];
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
        if (!$course->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 403);
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
