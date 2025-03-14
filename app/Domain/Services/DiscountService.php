<?php

namespace App\Domain\Services;

use App\Domain\Models\Discount;
use App\Domain\Repositories\DiscountRepositoryInterface;
use App\Http\Requests\DiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Shared\Traits\HandlesFileDeletion;
use App\Shared\Traits\HandlesMultipleFileUpload;
use App\Shared\Traits\HandlesMultipleImageUpload;
use App\Shared\Traits\ownershipAuthorization;

class DiscountService
{
    use HandlesMultipleImageUpload,
        HandlesMultipleFileUpload,
        HandlesFileDeletion,
        ownershipAuthorization;

    public function __construct(
        private PaginationService $paginationService,
        private DiscountRepositoryInterface $discountRepository
    ) {}

    /**
     * Get paginated discounts.
     */
    public function getDiscounts(int $perPage = 10, int $page = 1): array
    {
        $discounts = $this->discountRepository->get($perPage, $page);

        return [
            'data' => DiscountResource::collection($discounts),
            'metadata' => $this->paginationService->getPaginationData($discounts),
        ];
    }

    /**
     * Create a new discount.
     */
    public function createDiscount(DiscountRequest $request): array
    {
        $validatedData = $request->validated();
        $discount = $this->discountRepository->create($validatedData);
        $discount->Approval()->create(['status' => 'pending']);
        $discount->visibility()->create(['status' => 'private']);

        $this->handleFileUploads($request, $discount);

        return [
            'message' => 'تم إنشاء الخصم بنجاح',
            'data' => new DiscountResource($discount),
        ];
    }

    /**
     * Update an existing discount.
     */
    public function updateDiscount(Discount $discount, DiscountRequest $request): array
    {
        $this->authorizeOwnership($discount);
        $validatedData = $request->validated();

        $this->handleDeletedFiles($request);
        $this->discountRepository->update($discount, $validatedData);
        $this->handleFileUploads($request, $discount);

        return [
            'message' => 'تم تحديث الخصم بنجاح',
            'data' => new DiscountResource($discount),
        ];
    }

    /**
     * Get a discount by ID.
     */
    public function getDiscountById(int $id): Discount
    {
        return $this->discountRepository->findById($id) ?? abort(404, 'الخصم غير موجود');
    }

    /**
     * Delete a discount.
     */
    public function deleteDiscount(int $id, string $type = 'api'): array
    {
        $discount = $this->getDiscountById($id);
        $this->authorizeOwnership($discount, $type);

        $this->discountRepository->delete($discount);

        return ['message' => 'تم حذف الخصم بنجاح'];
    }

    /**
     * Search for discounts.
     */
    public function searchDiscount(string $searchTerm)
    {
        return DiscountResource::collection($this->discountRepository->search($searchTerm));
    }

    /**
     * Paginate discounts.
     */
    public function getPaginated(int $perPage)
    {
        return $this->discountRepository->paginate($perPage);
    }

    /**
     * Handle file uploads.
     */
    private function handleFileUploads(DiscountRequest $request, Discount $discount): void
    {
        $this->attachImages($request, $discount, 'discount/images', 'discount_');
        $this->attachFiles($request, $discount, 'discount/pdf', 'pdf_');
    }

    /**
     * Handle deleted files.
     */
    private function handleDeletedFiles(DiscountRequest $request): void
    {
        if ($request->filled('deleted_images_and_videos')) {
            $this->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        if ($request->filled('deleted_files')) {
            $this->deleteFiles($request->deleted_files, 'pdf');
        }
    }
}
