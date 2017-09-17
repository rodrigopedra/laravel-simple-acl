<?php

namespace RodrigoPedra\LaravelSimpleACL\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use RodrigoPedra\LaravelSimpleACL\Models\Permission;

class LoadSimpleACL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        $user = $request->user();

        if (is_null( $user )) {
            return $next( $request );
        }

        $user->load( [ 'roles', 'permissions' ] );

        $user->permissions->each( function ( Permission $permission ) use ( $user, $request ) {
            Gate::define( $permission->label, function () use ( $permission, $user, $request ) {
                return $user->hasPermission( $permission );
            } );
        } );

        return $next( $request );
    }
}
