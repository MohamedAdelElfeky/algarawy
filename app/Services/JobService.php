<?php

namespace App\Services;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }


    public function getAllJobs($perPage = 10, $page = 1)
    {
        $jobs = Job::paginate($perPage, ['*'], 'page', $page);
        $jobCollection = JobResource::collection($jobs);

        $paginationData = $this->paginationService->getPaginationData($jobs);

        return [
            'data' => $jobCollection,
            'metadata' => $paginationData,
        ];
    }



    public function getJobById($id)
    {
        $job = Job::find($id);
        if (!$job) {
            abort(404, 'لم يتم العثور على الوظيفة');
        }
        return $job;
    }

    public function createJob(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'location' => 'required|string|location',
            'contact_information' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_name' => 'required|string',            
            'company_location' => 'required|string|location',
            'company_type' => 'required|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'required|string',
            'is_training' => 'required|boolean',
            'is_full_time' => 'required|boolean',
            'price' => 'required|numeric',
            'job_status' => 'required|boolean',
        ]);
        $data['user_id'] = Auth::id();
        // Handle photo upload and save it temporarily
        // if (request()->hasFile('photo')) { 
        //     $photoPath = request()->file('photo')->store('temp', 'public');
        // } else {
        //     $photoPath = null;
        // }

        // Handle company logo upload and save it temporarily
        // if (request()->hasFile('company_logo')) { dd(request()->file('company_logo'));
        //     $logoPath = request()->file('company_logo')->store('temp', 'public');
        // } else {
        //     $logoPath = null;
        // }
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new job
        $job = Job::create($data);
        // if ($photoPath) {
        //     $newPhotoPath = "photos/{$job->id}/" . uniqid() . '.' . request()->file('photo')->getClientOriginalExtension();
        //     Storage::disk('public')->move($photoPath, $newPhotoPath);
        //     $job->update(['photo' => $newPhotoPath]);
        // }

        // if ($logoPath) {
        //     $newLogoPath = "logos/{$job->id}/" . uniqid() . '.' . request()->file('company_logo')->getClientOriginalExtension();
        //     Storage::disk('public')->move($logoPath, $newLogoPath);
        //     $job->update(['company_logo' => $newLogoPath]);
        // }

        return [
            'message' => 'تم إنشاء الوظيفة بنجاح',
            'data' => new JobResource($job),
        ];
    }

    public function updateJob(Job $job, array $data)
    {
        if (($job->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'sometimes|required',
            'qualifications' => 'sometimes|required',
            'location' => 'sometimes|required',
            'contact_information' => 'sometimes|required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_name' => 'sometimes|required|string',
            'company_location' => 'sometimes|required|string',
            'company_type' => 'sometimes|required|string',
            'company_link' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'sometimes|required|string',
            'is_training' => 'sometimes|required|boolean',
            'is_full_time' => 'sometimes|required|boolean',
            'price' => 'sometimes|required|numeric',
            'job_status' => 'sometimes|required|boolean',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $job->update($data);
        return response()->json([
            'message' => 'تم تحديث الوظيفة بنجاح',
            'data' => new JobResource($job),
        ]);
    }

    public function deleteJob(Job $job)
    {
        if (($job->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الوظيفية ليس من إنشائك',
            ], 200);
        }
        $job->delete();
        return response()->json([
            'message' => 'تم حذف الوظيفة بنجاح',
        ]);
    }

    public function searchJob($searchTerm)
    {
        $jobs = Job::where(function ($query) use ($searchTerm) {
            $fields = ['description', 'name', 'qualifications', 'contact_information', 'company_name', 'price'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return JobResource::collection($jobs);
    }
}
