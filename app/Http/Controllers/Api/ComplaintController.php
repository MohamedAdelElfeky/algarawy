<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function toggleComplaint(Request $request, $type, $id)
    {
        $user = Auth::user();
        $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];
        if (!in_array($type, $validModels)) {
            return response()->json(['message' => 'نوع النموذج غير صالح'], 400);
        }
        $modelClass = 'App\Models\\' . ucfirst($type);
        $model = $modelClass::find($id);
        if (!$model) {
            return response()->json(['message' => 'النموذج غير موجود'], 404);
        }
        $existingComplaint = $user->complaints()->where('complaintable_type', $modelClass)
            ->where('complaintable_id', $id)
            ->first();
        if ($existingComplaint) {
            return response()->json(['message' => 'تم أضافة شكوي من قبل', 'complaint' => false], 200);
        }
        $comment = $request->comment;
        if (empty($comment)) {
            return response()->json(['message' => 'يجب إدخال تعليق للشكوى'], 400);
        }
        $complaint = new Complaint();
        $complaint->complaintable_id = $id;
        $complaint->complaintable_type = $modelClass;
        $complaint->comment = $comment;
        $user->complaints()->save($complaint);
        return response()->json(['message' => 'تم أضافة شكوي', 'complaint' => true], 200);
    }

    public function showComplaints($type, $id)
    {
        $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];
        if (!in_array($type, $validModels)) {
            return response()->json(['message' => 'نوع النموذج غير صالح'], 400);
        }
    
        $modelClass = 'App\Models\\' . ucfirst($type);
        $model = $modelClass::find($id);
        if (!$model) {
            return response()->json(['message' => 'النموذج غير موجود'], 404);
        }
    
        $complaints = ComplaintResource::collection($model->complaints);
    
        return response()->json(['message' => 'قائمة الشكاوى', 'complaints' => $complaints], 200);
    }
}
