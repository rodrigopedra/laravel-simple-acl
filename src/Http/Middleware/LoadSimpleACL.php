<?php

namespace RodrigoPedra\LaravelSimpleACL\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LoadSimpleACL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        /** @var  \RodrigoPedra\LaravelSimpleACL\Concerns\HasACL $user */
        $user = $request->user();

        if (\is_null($user)) {
            return $next($request);
        }

        $user->loadACLCache();

        foreach ($user->permissions as $permission) {
            Gate::define($permission->label, fn () => $user->hasPermission($permission));
        }

        return $next($request);
    }
}
