<?php

namespace App\Http\Controllers;

use App\Domain\Models\PostApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function updateApprovalStatus(Request $request, $model, $id)
    {
        $allowedModels = ['service', 'job', 'discount', 'meeting', 'project', 'course'];
        if (!in_array($model, $allowedModels)) {
            return response()->json(['error' => 'نوع النموذج غير صالح'], 400);
        }

        $modelClass = '\\App\\Domain\\Models\\' . ucfirst($model);
        $approvable = $modelClass::findOrFail($id);

        PostApproval::updateApprovalStatus(
            $approvable,
            $request->status,
            Auth::id(),
            $request->notes
        );

        return response()->json(['message' => 'تم تحديث حالة الموافقة بنجاح.']);
    }
}
