<?php

namespace App\Services;

use App\Domain\Models\Discount;
use App\Domain\Models\Image;
use App\Http\Requests\DiscountRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DiscountResource;
use Illuminate\Support\Facades\Storage;

class DiscountService
{
    public function __construct(private PaginationService $paginationService, private FileHandlerService $fileHandler) {}

    public function getDiscounts($perPage = 10, $page = 1)
    {
        $user = Auth::guard('sanctum')->user();

        $discountQuery = Discount::query()->approvalStatus('approved')->orderByDesc('created_at');

        if ($user) {
            $showNoComplaintedPosts = $user->userSettings()
                ->whereHas('setting', fn($query) => $query->where('key', 'show_no_complaints_posts'))
                ->value('value') ?? false;

            $blockedUserIds = $user->blockedUsers()->pluck('blocked_user_id');

            $discountQuery->whereNotIn('user_id', $blockedUserIds);

            if ($showNoComplaintedPosts) {
                $discountQuery->where(
                    fn($query) =>
                    $query->where('user_id', $user->id)
                        ->orWhereDoesntHave('complaints')
                );
            }
        } else {
            $discountQuery->visibilityStatus();
        }

        $discounts = $discountQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => DiscountResource::collection($discounts),
            'metadata' => $this->paginationService->getPaginationData($discounts),
        ];
    }

    public function createDiscount(DiscountRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        $discount = Discount::create($validatedData);
        $this->fileHandler->attachImages($request, $discount, 'discount/images', 'course_');
        $this->fileHandler->attachPdfs($request, $discount, 'discount/pdf', 'pdf_');
        return [
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => new DiscountResource($discount),
        ];
    }

    public function updateDiscount(Discount $discount, DiscountRequest $request)
    {
        if (!$discount->isOwnedBy(auth()->user())) {
            return response()->json([
                'message' => 'هذا الدورة ليس من إنشائك',
            ], 403);
        }
        $validatedData = $request->validated();
        if ($request->filled('deleted_images_and_videos')) {
            $this->fileHandler->deleteFiles($request->deleted_images_and_videos, 'image');
        }
        if ($request->filled('deleted_files')) {
            $this->fileHandler->deleteFiles($request->deleted_files, 'pdf');
        }
        $discount->update($validatedData);
        $this->fileHandler->attachImages($request, $discount, 'discount/images', 'course_');
        $this->fileHandler->attachPdfs($request, $discount, 'discount/pdf', 'pdf_');
        return [
            'success' => true,
            'message' => 'تم تحديث الخصم بنجاح',
            'data' => new DiscountResource($discount),
        ];
    }



    public function getDiscountById($id): Discount
    {
        $discount = Discount::find($id);
        if (!$discount) {
            abort(404, 'الخصم غير موجود');
        }
        return $discount;
    }

    public function deleteDiscount(Discount $discount)
    {
        if ($discount->user_id != Auth::id()) {
            return response()->json([
                'message' => 'هذا الخصم ليس من إنشائك',
            ], 200);
        }
        $discount->delete();
        return response()->json(['message' => 'تم حذف الخصم بنجاح']);
    }
    public function searchDiscount($searchTerm)
    {
        $discounts = Discount::where(function ($query) use ($searchTerm) {
            $fields = ['description'];
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', '%' . $searchTerm . '%');
            }
        })->get();
        return  DiscountResource::collection($discounts);
    }
}
