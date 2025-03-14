<?php

namespace App\Domain\Services;

use App\Domain\Models\Course;
use App\Domain\Repositories\CourseRepositoryInterface;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesMultipleFileUpload;
use App\Shared\Traits\HandlesMultipleImageUpload;
use App\Shared\Traits\ownershipAuthorization;

class CourseService
{
    use HandlesMultipleImageUpload,
        HandlesMultipleFileUpload,
        HandlesFileDeletion,
        ownershipAuthorization;

    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private PaginationService $paginationService
    ) {}

    /**
     * Get paginated list of courses
     */
    public function getCourses(int $perPage = 10, int $page = 1): array
    {
        $courses = $this->courseRepository->get($perPage, $page);

        return [
            'data' => CourseResource::collection($courses),
            'metadata' => $this->paginationService->getPaginationData($courses),
        ];
    }

    /**
     * Create a new course
     */
    public function createCourse(CourseRequest $request): array
    {
        $validatedData = $request->validated();
        $course = $this->courseRepository->create($validatedData);

        $course->Approval()->create(['status' => 'pending']);
        $course->visibility()->create(['status' => 'private']);

        $this->handleMediaUploads($request, $course);

        return [
            'message' => 'تم إنشاء الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    /**
     * Update an existing course
     */
    public function updateCourse(Course $course, CourseRequest $request): array
    {
        $this->authorizeOwnership($course);

        $validatedData = $request->validated();
        $this->handleFileDeletions($request);
        $this->courseRepository->update($course, $validatedData);
        $this->handleMediaUploads($request, $course);

        return [
            'message' => 'تم تحديث الدورة التدريبية بنجاح',
            'data' => new CourseResource($course),
        ];
    }

    /**
     * Retrieve course by ID
     */
    public function getCourseById(int $id): ?Course
    {
        return $this->courseRepository->findById($id);
    }

    /**
     * Delete a course
     */
    public function deleteCourse(int $id, string $type = 'api'): array
    {
        $course = $this->getCourseById($id);
        $this->authorizeOwnership($course, $type);

        $this->courseRepository->delete($course);

        return ['message' => 'تم حذف الدورة بنجاح'];
    }

    /**
     * Search courses
     */
    public function searchCourse(string $searchTerm)
    {
        return CourseResource::collection($this->courseRepository->search($searchTerm));
    }

    /**
     * Get paginated courses
     */
    public function getPaginated(int $perPage)
    {
        return $this->courseRepository->paginate($perPage);
    }

    /**
     * Handle media (images/files) uploads
     */
    private function handleMediaUploads(CourseRequest $request, Course $course): void
    {
        $this->attachImages($request, $course, 'courses/images', 'course_');
        $this->attachFiles($request, $course, 'courses/pdf', 'pdf_');
    }

    /**
     * Handle file deletions
     */
    private function handleFileDeletions(CourseRequest $request): void
    {
        if ($request->filled('deleted_images_and_videos')) {
            $this->deleteFiles($request->deleted_images_and_videos, 'image');
        }

        if ($request->filled('deleted_files')) {
            $this->deleteFiles($request->deleted_files, 'pdf');
        }
    }
}
