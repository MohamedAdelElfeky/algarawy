<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceResource;
use App\Models\Image;
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
            'images_or_video.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'nullable|string|location',
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
        if (($service->user_id) != Auth::id()); {
            return response()->json([
                'message' => 'هذا الخدمة ليس من إنشائك',
            ], 200);
        }
        $validator = Validator::make($data, [
            'description' => 'sometimes|nullable|string',
            'images_or_video' => 'nullable',
            'images_or_video.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'nullable|string|location',
            'deleted_images_and_videos' => 'nullable',
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
        $services = Service::paginate($perPage, ['*'], 'page', $page);
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
            abort(404, 'الخدمة غير موجودة');
        }
        return $service;
    }

    public function deleteService(string $id)
    {
        $service = Service::findOrFail($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        if (($service->user_id) != Auth::id()); {
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
