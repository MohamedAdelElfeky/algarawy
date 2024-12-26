<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1); 

        $user = Auth::user();

        if ($user) {
            $discounts = $this->discountService->getAllDiscounts($perPage, $page);
        } else {
            $discounts = $this->discountService->getAllDiscountsPublic($perPage, $page);
        }

        return response()->json($discounts, 200);
    }
    // public function index(Request $request)
    // {
    //     $perPage = $request->header('per_page');
    //     $page = $request->header('page');
    //     return response()->json($discounts, 200);
    // }

    public function getDiscounts(Request $request)
    {
        $perPage = $request->header('per_page');
        $page = $request->header('page');
        $discounts = $this->discountService->getAllDiscountsPublic($perPage, $page);
        return response()->json($discounts, 200);
    }

    public function show($id)
    {
        $discount = $this->discountService->getDiscountById($id);
        return new DiscountResource($discount);
    }

    public function store(Request $request)
    {
        $result = $this->discountService->createDiscount($request->all());
        return new DiscountResource($result['data']);
    }

    public function update(Request $request, $id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return response()->json(['message' => 'الخصم غير موجود'], 404);
        }

        $result = $this->discountService->updateDiscount($discount, $request->all());

        return new DiscountResource($result['data']);
    }

    public function destroy($id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return response()->json(['message' => 'الخصم غير موجود'], 404);
        }

        $this->discountService->deleteDiscount($discount);

        return response()->json(['message' => 'تم حذف الخصم بنجاح']);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $results = $this->discountService->searchDiscount($searchTerm);
        return response()->json(['data' => $results]);
    }
}
