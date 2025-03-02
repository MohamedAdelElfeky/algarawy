<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function toggleComplaint(Request $request, $type, $id)
    {
        $user = Auth::user();
        $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];
        if (!in_array($type, $validModels)) {
            return response()->json(['message' => 'نوع النموذج غير صالح'], 400);
        }
        $modelClass = 'App\Domain\Models\\' . ucfirst($type);
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

    public function editComplaint(Request $request, $complaintId)
    {
        $user = Auth::user();
        $complaint = Complaint::find($complaintId);
        // Check if the complaint exists and belongs to the logged-in user
        if (!$complaint || $complaint->user_id !== $user->id) {
            return response()->json(['message' => 'الشكوى غير موجودة أو لا تمتلكها'], 404);
        }

        // Validate the comment
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        // Update the complaint
        $complaint->update($validated);

        return response()->json(['message' => 'تم تعديل الشكوى بنجاح', 'complaint' => new ComplaintResource($complaint)], 200);
    }

    // Delete complaint
    public function deleteComplaint($complaintId)
    {
        $user = Auth::user();
        $complaint = Complaint::find($complaintId);

        if (!$complaint || $complaint->user_id !== $user->id) {
            return response()->json(['message' => 'الشكوى غير موجودة أو لا تمتلكها'], 404);
        }

        // Delete the complaint
        $complaint->delete();

        return response()->json(['message' => 'تم حذف الشكوى بنجاح'], 200);
    }
}
