<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\DiscountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountRequest;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $DiscountService)
    {
        $this->middleware('optional.auth')->only('index');
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $perPage = $request->header('per_page', 10);
        $page = $request->header('page', 1);
        $Discounts = $this->DiscountService->getDiscounts($perPage, $page);
        return response()->json($Discounts, 200);
    }

    public function show($id)
    {
        return new DiscountResource($this->DiscountService->getDiscountById($id));
    }

    public function store(DiscountRequest $request)
    {
        $Discount = $this->DiscountService->createDiscount($request);
        return response()->json($Discount, 201);
    }

    public function update(DiscountRequest $request, $id)
    {
        $Discount = $this->DiscountService->getDiscountById($id);
        $updatedDiscount = $this->DiscountService->updateDiscount($Discount, $request);
        return response()->json($updatedDiscount);
    }

    public function destroy($id)
    {
        return $this->DiscountService->deleteDiscount($id);
    }

    public function search(Request $request)
    {
        return $this->DiscountService->searchDiscount($request->get('search'));
    }
}
