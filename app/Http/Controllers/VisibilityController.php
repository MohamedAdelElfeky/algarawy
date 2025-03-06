<?php

namespace App\Http\Controllers;

use App\Domain\Models\Visibility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VisibilityController extends Controller
{
    public function updateVisibilityStatus(Request $request, $model, $id)
    {
        $allowedModels = ['service', 'job', 'discount', 'meeting', 'project', 'course'];
        if (!in_array($model, $allowedModels)) {
            return response()->json(['error' => 'نوع النموذج غير صالح.'], 400);
        }

        $modelClass = '\\App\\Domain\\Models\\' . ucfirst($model);
        $visible = $modelClass::findOrFail($id);

        Visibility::updateVisibilityStatus(
            $visible,
            $request->status,
        );

        return response()->json(['message' => 'تم تحديث الحالة المرئية بنجاح.']);
    }
}
