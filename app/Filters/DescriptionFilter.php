<?php


namespace App\Filters;
use Closure;

class DescriptionFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('description')){
            return $next($request)
                ->where('description','LIKE','%'.request('description').'%');
        }
        return $next($request);
    }
}
