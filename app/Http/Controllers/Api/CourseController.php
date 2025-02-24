<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->middleware('auth:sanctum');

        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return redirect()->route('courses.authenticated', ['perPage' => $perPage, 'page' => $page]);
        } else {
            $courses = $this->courseService->getAllCoursesPublic($perPage, $page);
        }

        return response()->json($courses, 200);
    }

    public function getAuthenticatedCourses(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);

        $user = Auth::user();

        if ($user) {
            $courses = $this->courseService->getAllCourses($perPage, $page);
            return response()->json($courses, 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }

    // public function index(Request $request)
    // {
    //     $perPage = $request->header('per_page');
    //     $page = $request->header('page');
    //     $courses = $this->courseService->getAllCourses($perPage, $page);
    //     return response()->json($courses, 200);
    // }

    public function getCourses(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $courses = $this->courseService->getAllCoursesPublic($perPage, $page);
        return response()->json($courses, 200);
    }
    public function show($id)
    {
        $course = new CourseResource($this->courseService->getCourseById($id));
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
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->courseService->searchCourse($searchTerm);
        return response()->json(['data' => $results]);
    }
}
