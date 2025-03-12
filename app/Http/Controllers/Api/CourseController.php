<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct(private CourseService $courseService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $courses = $this->courseService->getCourses($perPage, $page);
        return response()->json($courses, 200);
    }

    public function show($id)
    {
        $course = new CourseResource($this->courseService->getCourseById($id));
        return response()->json(['data' => $course], 200);
    }

    public function store(CourseRequest $request)
    {
        $course = $this->courseService->createCourse($request);
        return response()->json($course);
    }

    public function update(CourseRequest $request, $id)
    {
        $course = $this->courseService->getCourseById($id);
        $result = $this->courseService->updateCourse($course, $request);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->courseService->deleteCourse($id);
        return response()->json($result);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->courseService->searchCourse($searchTerm);
        return response()->json(['data' => $results]);
    }
}
