<?php

namespace App\Domain\Services;

use App\Domain\Models\Service;
use App\Domain\Repositories\ServiceRepositoryInterface;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Shared\Traits\HandlesMultipleImageUpload;
use App\Shared\Traits\HandlesMultipleFileUpload;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\ownershipAuthorization;

class ServiceService
{
    use HandlesMultipleImageUpload,
        HandlesMultipleFileUpload,
        HandlesFileDeletion,
        ownershipAuthorization;

    public function __construct(
        private ServiceRepositoryInterface $serviceRepository,
        private PaginationService $paginationService,
    ) {}

    public function getServices(int $perPage = 10, int $page = 1): array
    {
        $services = $this->serviceRepository->get($perPage, $page);
        return [
            'data' => ServiceResource::collection($services),
            'metadata' => $this->paginationService->getPaginationData($services),
        ];
    }

    public function getServiceById(int $id): Service
    {
        return $this->serviceRepository->findById($id);
    }

    public function createService(ServiceRequest $request): array
    {
        $validatedData = $request->validated();
        $service = $this->serviceRepository->create($validatedData);
        $this->handleFileAttachments($request, $service);

        return [
            'message' => 'تم إنشاء الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }

    public function updateService(Service $service, ServiceRequest $request): array
    {
        $this->authorizeOwnership($service);
        $validatedData = $request->validated();
        if ($request->filled('deleted_images_and_videos')) {
            $this->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        $this->serviceRepository->update($service, $validatedData);
        $this->handleFileAttachments($request, $service);

        return [
            'message' => 'تم تحديث الخدمة بنجاح',
            'data' => new ServiceResource($service),
        ];
    }

    public function deleteService(Service $service, string $type = 'api'): array
    {
        $this->authorizeOwnership($service, $type);
        $this->serviceRepository->delete($service, $type);

        return [
            'message' => 'تم حذف الخدمة بنجاح'
        ];
    }


    public function searchService(string $searchTerm)
    {
        return ServiceResource::collection($this->serviceRepository->search($searchTerm));
    }
    public function getPaginatedServices(int $perPage)
    {
        return $this->serviceRepository->paginate($perPage);
    }
    /**
     * Handle file and image attachments for a service.
     */
    private function handleFileAttachments(ServiceRequest $request, Service $service): void
    {
        $this->attachImages($request, $service, 'service/images', 'service_');
        $this->attachFiles($request, $service, 'service/files', 'file_');
    }
}
