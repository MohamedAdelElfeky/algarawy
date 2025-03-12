<?php

namespace App\Domain\Services;

use App\Domain\Models\BlockedUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BlockedUserService
{
    public function toggleBlock(int $userId, int $blockedUserId): bool
    {
        $existingRecord = BlockedUser::where(['user_id' => $userId, 'blocked_user_id' => $blockedUserId])->first();
        
        if ($existingRecord) {
            $existingRecord->delete();
            return false;
        }
        
        BlockedUser::create(['user_id' => $userId, 'blocked_user_id' => $blockedUserId]);
        return true;
    }

    public function getBlockedUsers(User $user): Collection
    {
        return $user->blockedUsers()->get();
    }
}
