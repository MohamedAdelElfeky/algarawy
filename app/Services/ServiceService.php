<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceResource;
use App\Models\Image;

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
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'required|string|location',
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
        if (request()->hasFile('images')) {
            foreach (request()->file('images') as $image) {
                $imagePath = $image->store('images/Service/img', 'public');
                $imageType = $image->getClientOriginalExtension();
                $mimeType = $image->getMimeType();
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
            'description' => 'sometimes|required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'location' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }
        $data['user_id'] = Auth::id();
        $service->update($data);

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
