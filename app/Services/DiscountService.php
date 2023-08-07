<?php

namespace App\Services;

use App\Models\Discount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DiscountResource;

class DiscountService
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function getAllDiscounts($perPage = 10, $page = 1)
    {
        $discounts = Discount::paginate($perPage, ['*'], 'page', $page);
        $discountResource = DiscountResource::collection($discounts);
        $paginationData = $this->paginationService->getPaginationData($discounts);
        return [
            'data' => $discountResource,
            'metadata' => $paginationData,
        ];
    }

    public function createDiscount(array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'required',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'string|location',
            'discount' => 'nullable',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        $data['user_id'] = Auth::id();
        $discount = Discount::create($data);

        return [
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => new DiscountResource($discount),
        ];
    }

    public function updateDiscount(Discount $discount, array $data): array
    {
        $validator = Validator::make($data, [
            'description' => 'sometimes|required',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,mp4|max:2048',
            'location' => 'required|string|location',
            'discount' => 'nullable',
            'price' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ];
        }

        $data['user_id'] = Auth::id();
        $discount->update($data);

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

    public function deleteDiscount(string $id)
    {
        $discount = Discount::findOrFail($id);

        if (!$discount) {
            return response()->json(['message' => 'الخصم غير موجود'], 404);
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
