<?php

namespace App\Http\Controllers;

use App\Domain\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InvalidModelException;
use App\Http\Requests\ApprovalStatusRequest;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function updateApprovalStatus(ApprovalStatusRequest $request, string $model, int $id)
    {
        try {
            $request->validated();
            $this->approvalService->updateStatus($model, $id, $request->status, Auth::id(), $request->notes);

            return response()->json(['message' => 'تم تحديث حالة الموافقة بنجاح.']);
        } catch (InvalidModelException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ غير متوقع.'], 500);
        }
    }
}
