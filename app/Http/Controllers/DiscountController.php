<?php

namespace App\Http\Controllers;

use App\Domain\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{

    public function __construct(private DiscountService $discountService) {}

    public function index(): View
    {
        $discounts = $this->discountService->getPaginated(25);
        return view('pages.dashboards.discount.index', compact('discounts'));
    }


    public function destroy(int $id): JsonResponse
    {

        $deleted = $this->discountService->deleteDiscount($id, 'web');
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'deleted successfully.' : 'Failed to delete.'
        ]);
    }
}
