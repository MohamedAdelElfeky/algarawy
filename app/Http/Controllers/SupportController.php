<?php

namespace App\Http\Controllers;

use App\Domain\Services\SupportService;
use App\Http\Requests\SupportRequest;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    public function __construct(private SupportService $supportService) {}

    public function index()
    {
        $support = $this->supportService->getSupportDetails();

        return view('pages.dashboards.support.index', [
            'number' => $support?->number,
            'email' => $support?->email,
        ]);
    }

    public function addOrUpdateNumber(SupportRequest $request)
    {
        $this->supportService->updateSupportDetails($request->validated());
        return redirect()->route('support')->with('success', 'تمت إضافة / تحديث الدعم بنجاح');
    }

    public function numberSupport(): JsonResponse
    {
        $support = $this->supportService->getSupportDetails();
        $number = $support?->number;

        return response()->json(['number' => $number]);
    }
}
