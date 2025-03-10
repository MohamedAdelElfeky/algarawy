<?php

namespace App\Services;

use App\Domain\Models\Image;
use App\Domain\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function createService(array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'nullable|string',
            'images_or_video' => 'nullable',
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4',
            'location' => 'nullable|string|location',
            'status' => 'nullable',

        ]);

        if ($validator->fails()) {
            return [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        $data['user_id'] = Auth::id();
        $service = Service::create($data);
        // Handle images/videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_service.' . $image->getClientOriginalExtension();
                $image->move(public_path('service/images/'), $file_name);
                $imagePath = "service/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $service->images()->save($imageObject);
            }
        }

        return [
            'message' => 'تم إنشاء الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }

    public function updateService(Service $service, array $data): array
    {

        if ($service->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الخدمة ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'sometimes|nullable|string',
            'images_or_video' => 'nullable',
            'images_or_video.*' => 'file|mimes:jpeg,png,jpg,gif,mp4',
            'location' => 'nullable|string|location',
            'deleted_images_and_videos' => 'nullable',
            'status' => 'nullable',

        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $deletedImagesAndVideos = $data['deleted_images_and_videos'] ?? [];
        foreach ($deletedImagesAndVideos as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                // Delete from storage
                Storage::delete($image->url);
                // Delete from database
                $image->delete();
            }
        }

        $service->update($data);
        // Handle images/videos
        if (request()->hasFile('images_or_video')) {
            foreach (request()->file('images_or_video') as $key => $item) {
                $image = $data['images_or_video'][$key];
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
                $file_name = time() . rand(0, 9999999999999) . '_service.' . $image->getClientOriginalExtension();
                $image->move(public_path('service/images/'), $file_name);
                $imagePath = "service/images/" . $file_name;
                $imageObject = new Image([
                    'url' => $imagePath,
                    'mime' => $mimeType,
                    'image_type' => $imageType,
                ]);
                $service->images()->save($imageObject);
            }
        }
        return [
            'message' => 'تم تحديث الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }

    public function getAllServices($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $showNoComplaintedPosts = $user->userSettings()
            ->whereHas('setting', function ($query) {
                $query->where('key', 'show_no_complaints_posts');
            })
            ->value('value') ?? false;

        $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id')->toArray();

        $servicesQuery = Service::whereNotIn('user_id', $blockedUserIds)->ApprovalStatus('approved')
            ->orderBy('created_at', 'desc');

        if ($showNoComplaintedPosts) {
            $servicesQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereDoesntHave('complaints');
            });
        }
        $services = $servicesQuery->paginate($perPage, ['*'], 'page', $page);
        $serviceResource = ServiceResource::collection($services);
        $paginationData = $this->paginationService->getPaginationData($services);

        return [
            'data' => $serviceResource,
            'metadata' => $paginationData,
        ];
    }

    public function getAllServicesPublic($perPage = 10, $page = 1)
    {
        $servicesQuery = Service::visibilityStatus('public')->ApprovalStatus('approved')
            ->orderBy('created_at', 'desc');
        $services = $servicesQuery->paginate($perPage, ['*'], 'page', $page);
        $serviceResource = ServiceResource::collection($services);
        $paginationData = $this->paginationService->getPaginationData($services);

        return [
            'data' => $serviceResource,
            'metadata' => $paginationData,
        ];
    }

    public function getServiceById($id): Service
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return $service;
    }

    public function deleteService(Service $service)
    {
        if ($service->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الخدمة ليس من إنشائك',
            ], 200);
        }
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
    public function searchService($searchTerm)
    {
        $services = Service::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return ServiceResource::collection($services);
    }
}
