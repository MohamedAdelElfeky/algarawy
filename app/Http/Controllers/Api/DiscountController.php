<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        $discounts = $this->discountService->getAllDiscounts();
        return DiscountResource::collection($discounts);
    }

    public function show($id)
    {
        $discount = $this->discountService->getDiscountById($id);
        return new DiscountResource($discount);
    }

    public function store(Request $request)
    {
        $result = $this->discountService->createDiscount($request->all());
        if ($result['success']) {
            return new DiscountResource($result['data']);
        } else {
            return response()->json([
                'message' => 'Failed to create discount',
                'errors' => $result['errors'],
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }

        $result = $this->discountService->updateDiscount($discount, $request->all());

        if ($result['success']) {
            return new DiscountResource($result['data']);
        } else {
            return response()->json([
                'message' => 'Failed to update discount',
                'errors' => $result['errors'],
            ], 422);
        }
    }

    public function destroy($id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }

        $this->discountService->deleteDiscount($discount);

        return response()->json(['message' => 'Discount deleted successfully']);
    }
}
