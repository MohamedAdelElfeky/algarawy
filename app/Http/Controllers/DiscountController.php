<?php

namespace App\Http\Controllers;

use App\Domain\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        $discounts = Discount::with([
            'user',
            'images',
            'pdfs',
            'likes',
            'favorites',
        ])->orderBy('created_at', 'desc')->paginate(25);
        return view('pages.dashboards.discount.index', compact('discounts'));
    }

    public function destroy($id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return back()->with('error', 'Discount not found');
        }

        $this->discountService->deleteDiscount($discount);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
}
