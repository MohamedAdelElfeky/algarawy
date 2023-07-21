<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(): JsonResponse
    {
        $courses = $this->courseService->getAllCourses();
        return response()->json(['courses' => $courses]);
    }

    public function show($id): JsonResponse
    {
        $course = $this->courseService->getCourseById($id);
        return response()->json(['course' => $course]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->courseService->createCourse($data);
        return response()->json($result);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->all();
        $course = $this->courseService->getCourseById($id);
        $result = $this->courseService->updateCourse($course, $data);
        return response()->json($result);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->courseService->deleteCourse($id);
        return response()->json($result);
    }
}
