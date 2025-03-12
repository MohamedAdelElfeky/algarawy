<?php

namespace App\Http\Controllers;

use App\Domain\Models\Course;
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
        ])->orderBy('created_at', 'desc')->paginate(25);
        return view('pages.dashboards.course.index', compact('courses'));

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
   
}
