<?php

namespace App\Filters;

use Closure;

class ApprovalStatusFilter
{
    public function handle($query, Closure $next)
    {
        $query->approvalStatus('approved')->orderByDesc('created_at');
        return $next($query);
    }
}
