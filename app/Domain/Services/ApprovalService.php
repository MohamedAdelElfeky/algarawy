<?php

namespace App\Domain\Services;

use App\Domain\Models\PostApproval;
use App\Exceptions\InvalidModelException;

class ApprovalService
{
    protected array $allowedModels = ['service', 'job', 'discount', 'meeting', 'project', 'course'];

    public function updateStatus(string $model, int $id, string $status, int $userId, ?string $notes)
    {
        if (!in_array($model, $this->allowedModels)) {
            throw new InvalidModelException('نوع النموذج غير صالح');
        }

        $modelClass = '\\App\\Domain\\Models\\' . ucfirst($model);
        if (!class_exists($modelClass)) {
            throw new InvalidModelException('النموذج المحدد غير موجود.');
        }

        $approvable = $modelClass::findOrFail($id);

        PostApproval::updateApprovalStatus($approvable, $status, $userId, $notes);
    }
}
