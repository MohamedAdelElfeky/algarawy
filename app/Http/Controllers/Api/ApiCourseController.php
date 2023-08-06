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

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $courses = $this->courseService->getAllCourses($perPage, $page);
        return response()->json($courses, 200);
    }

    public function show($id)
    {
        $course = $this->courseService->getCourseById($id);
        return response()->json(['data' => $course], 200);
    }

    public function store(Request $request)
    {
        // return $request->file('files');
        $course = $this->courseService->createCourse($request->all());
        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $course = $this->courseService->getCourseById($id);
        $result = $this->courseService->updateCourse($course, $request->all());
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->courseService->deleteCourse($id);
        return response()->json($result);
    }
}
