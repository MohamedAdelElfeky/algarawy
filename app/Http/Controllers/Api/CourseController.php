<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\CourseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $CourseService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $Courses = $this->CourseService->getCourses($perPage, $page);
        return response()->json($Courses, 200);
    }

    public function show($id)
    {
        return new CourseResource($this->CourseService->getCourseById($id));
    }

    public function store(CourseRequest $request)
    {
        $Course = $this->CourseService->createCourse($request);
        return response()->json($Course, 201);
    }

    public function update(CourseRequest $request, $id)
    {
        $Course = $this->CourseService->getCourseById($id);
        $updatedCourse = $this->CourseService->updateCourse($Course, $request);
        return response()->json($updatedCourse);
    }

    public function destroy($id)
    {
        return $this->CourseService->deleteCourse($id);
    }

    public function search(Request $request)
    {
        return $this->CourseService->searchCourse($request->get('search'));
    }
}