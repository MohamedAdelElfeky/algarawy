<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with([
            'user', 'images', 'pdfs', 'likes', 'favorites',
        ])->orderBy('created_at', 'desc')->get();
        return view('pages.dashboards.course.index', compact('courses'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $response = $this->courseService->createCourse($data);

        if ($response['success']) {
            return response()->json([
                'message' => $response['message'],
                'data' => $response['data'],
            ]);
        }

        return response()->json([
            'message' => $response['message'],
            'errors' => $response['errors'],
        ], 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json([
            'message' => 'Course retrieved successfully',
            'data' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $course = Course::findOrFail($id);

        $response = $this->courseService->updateCourse($course, $data);

        if ($response['success']) {
            return response()->json([
                'message' => $response['message'],
                'data' => $response['data'],
            ]);
        }

        return response()->json([
            'message' => $response['message'],
            'errors' => $response['errors'],
        ], 422);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->courseService->deleteCourse($id);
        return response()->json([
            'message' => $response['message'],
        ]);
    }

    public function changeStatus(Request $request, Course $course)
    {
        $request->validate([
            'status' => 'in:public,private',
        ]);

        $course->update(['status' => $request->status]);

        return back()->with('status', 'Course status updated successfully!');
    }
}
