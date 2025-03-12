<?php

namespace App\Domain\Repositories;


class ComplaintRepository
{
    private array $validModels = ['course', 'job', 'discount', 'meeting', 'project', 'service'];

    public function getModelInstance(string $type, int $id)
    {
        if (!in_array($type, $this->validModels)) {
            return null;
        }

        $modelClass = "App\Domain\Models\\" . ucfirst($type);
        return $modelClass::find($id);
    }

    public function userHasComplaint($user, string $modelClass, int $id): bool
    {
        return $user->complaints()->where('complaintable_type', $modelClass)
            ->where('complaintable_id', $id)
            ->exists();
    }

    public function createComplaint($user, int $id, string $modelClass, string $comment)
    {
        return $user->complaints()->create([
            'complaintable_id' => $id,
            'complaintable_type' => $modelClass,
            'comment' => $comment,
        ]);
    }

    public function getUserComplaint($user, int $complaintId)
    {
        return $user->complaints()->find($complaintId);
    }

    public function updateComplaint($complaint, array $data)
    {
        $complaint->update($data);
    }

    public function deleteComplaint($complaint)
    {
        $complaint->delete();
    }
}
