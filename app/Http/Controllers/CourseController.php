<?php

namespace App\Http\Controllers;

use App\Domain\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CourseController extends Controller
{


    public function __construct(private CourseService $courseService) {}

    public function index(): View
    {
        $courses = $this->courseService->getPaginated(25);
        return view('pages.dashboards.course.index', compact('courses'));
    }


    public function destroy(int $id): JsonResponse
    {
        
        $deleted = $this->courseService->deleteCourse($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'deleted successfully.' : 'Failed to delete.'
        ]);
    }
}
