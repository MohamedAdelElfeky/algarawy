<?php

namespace App\Services;

use App\Domain\Models\Image;
use App\Domain\Models\Service;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;

class ServiceService
{

    public function __construct(private PaginationService $paginationService, private FileHandlerService $fileHandler) {}

    public function createService(ServiceRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $service = Service::create($validatedData);
        $this->fileHandler->attachImages(request(), $service, 'service/images', 'project_');
        return [
            'message' => 'تم إنشاء الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }

    public function updateService(Service $service, ServiceRequest $request)
    {

        if (!$service->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الخدمة ليس من إنشائك',
            ], 403);
        }

        $validatedData = $request->validated();
        if ($request->filled('deleted_images_and_videos')) {
            $this->fileHandler->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        $service->update($validatedData);
        $this->fileHandler->attachImages(request(), $service, 'service/images', 'project_');
        return [
            'message' => 'تم تحديث الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }
    public function getServices($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();

        $serviceQuery = Service::query()->approvalStatus('approved')->orderByDesc('created_at');

        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $serviceQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $serviceQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $serviceQuery->visibilityStatus();
        }

        $services = $serviceQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => ServiceResource::collection($services),
            'metadata' => $this->paginationService->getPaginationData($services),
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
        if (!$service->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الخدمة ليس من إنشائك',
            ], 403);
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
