<?php

namespace App\Applications\Services;

use App\Domain\Repositories\ComplaintRepository;
use App\Http\Resources\ComplaintResource;

class ComplaintService
{
    private ComplaintRepository $complaintRepository;

    public function __construct(ComplaintRepository $complaintRepository)
    {
        $this->complaintRepository = $complaintRepository;
    }

    public function toggleComplaint($user, string $type, int $id, array $data)
    {
        $model = $this->complaintRepository->getModelInstance($type, $id);
        if (!$model) {
            return ['data' => ['message' => 'النموذج غير موجود'], 'status' => 404];
        }

        if ($this->complaintRepository->userHasComplaint($user, get_class($model), $id)) {
            return ['data' => ['message' => 'تم أضافة شكوي من قبل', 'complaint' => false], 'status' => 200];
        }

        $complaint = $this->complaintRepository->createComplaint($user, $id, get_class($model), $data['comment']);

        return ['data' => ['message' => 'تم أضافة شكوي', 'complaint' => new ComplaintResource($complaint)], 'status' => 201];
    }

    public function getComplaints(string $type, int $id)
    {
        $model = $this->complaintRepository->getModelInstance($type, $id);
        if (!$model) {
            return ['data' => ['message' => 'النموذج غير موجود'], 'status' => 404];
        }

        return [
            'data' => [
                'message' => 'قائمة الشكاوى',
                'complaints' => ComplaintResource::collection($model->complaints),
            ],
            'status' => 200
        ];
    }

    public function updateComplaint($user, int $complaintId, array $data)
    {
        $complaint = $this->complaintRepository->getUserComplaint($user, $complaintId);
        if (!$complaint) {
            return ['data' => ['message' => 'الشكوى غير موجودة أو لا تمتلكها'], 'status' => 404];
        }

        $this->complaintRepository->updateComplaint($complaint, $data);

        return ['data' => ['message' => 'تم تعديل الشكوى بنجاح', 'complaint' => new ComplaintResource($complaint)], 'status' => 200];
    }

    public function deleteComplaint($user, int $complaintId)
    {
        $complaint = $this->complaintRepository->getUserComplaint($user, $complaintId);
        if (!$complaint) {
            return ['data' => ['message' => 'الشكوى غير موجودة أو لا تمتلكها'], 'status' => 404];
        }

        $this->complaintRepository->deleteComplaint($complaint);

        return ['data' => ['message' => 'تم حذف الشكوى بنجاح'], 'status' => 200];
    }
}
