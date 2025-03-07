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

    public function show($id)
    {
        $discount = $this->discountService->getDiscountById($id);
        return view('discounts.show', compact('discount'));
    }
    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {

        $userId = auth()->user()->id;
        $result = $this->discountService->createDiscount($request->all());

        if ($result['success']) {
            return redirect()->route('discounts.show', $result['data']->id)
                ->with('success', 'Discount created successfully.');
        } else {
            return back()->withErrors($result['errors'])->withInput();
        }
    }

    public function edit($id)
    {
        $discount = $this->discountService->getDiscountById($id);
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $discount = $this->discountService->getDiscountById($id);

        if (!$discount) {
            return back()->with('error', 'Discount not found');
        }

        $result = $this->discountService->updateDiscount($discount, $request->all());

        if ($result['success']) {
            return redirect()->route('discounts.show', $discount->id)
                ->with('success', 'Discount updated successfully.');
        } else {
            return back()->withErrors($result['errors'])->withInput();
        }
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

    public function changeStatus(Request $request, Discount $discount)
    {
        $request->validate([
            'status' => 'in:public,private',
        ]);

        $discount->update(['status' => $request->status]);

        return back()->with('status', 'Discount status updated successfully!');
    }
}
