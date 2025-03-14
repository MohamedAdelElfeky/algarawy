<?php

namespace App\Http\Controllers\Api;

use App\Applications\Services\ComplaintService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintRequest;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{

    public function __construct(private ComplaintService $complaintService)
    {
        $this->middleware('auth:sanctum');
    }

    public function toggleComplaint(ComplaintRequest $request, string $type, int $id)
    {
        $result = $this->complaintService->toggleComplaint(Auth::user(), $type, $id, $request->validated());

        return response()->json($result['data'], $result['status']);
    }

    public function showComplaints(string $type, int $id)
    {
        $result = $this->complaintService->getComplaints($type, $id);

        return response()->json($result['data'], $result['status']);
    }

    public function editComplaint(ComplaintRequest $request, int $complaintId)
    {
        $result = $this->complaintService->updateComplaint(Auth::user(), $complaintId, $request->validated());

        return response()->json($result['data'], $result['status']);
    }

    public function deleteComplaint(int $complaintId)
    {
        $result = $this->complaintService->deleteComplaint(Auth::user(), $complaintId);

        return response()->json($result['data'], $result['status']);
    }
}
