<?php

namespace App\Shared\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait ownershipAuthorization
{
    /**
     * @param  mixed  $model
     * @param  string  $type
     * @throws AuthorizationException
     */
    public function authorizeOwnership($model, string $type = 'api'): void
    {
        if (!$model || ($type === 'api' && !$model->isOwnedBy(auth()->user()))) {
            abort(403, 'هذا العنصر ليس من إنشائك');
        }
    }
}
