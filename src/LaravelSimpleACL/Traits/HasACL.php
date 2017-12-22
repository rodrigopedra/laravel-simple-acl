<?php

namespace RodrigoPedra\LaravelSimpleACL\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use RodrigoPedra\LaravelSimpleACL\Models\Permission;
use RodrigoPedra\LaravelSimpleACL\Models\Role;

trait HasACL
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany( Permission::class, 'permission_user' )->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany( Role::class, 'role_user' )->withTimestamps();
    }

    public function rebuildPermissions()
    {
        unset( $this->roles );
        unset( $this->permissions );

        Cache::tags( [ 'acl' ] )->forget( $this->getACLCacheKey( 'roles' ) );
        Cache::tags( [ 'acl' ] )->forget( $this->getACLCacheKey( 'permissions' ) );

        $permissionsIds = $this->roles()->with( 'permissions' )->get()
            ->pluck( 'permissions' )
            ->flatten( 1 )
            ->pluck( 'id' )
            ->unique()
            ->toArray();

        $this->permissions()->sync( $permissionsIds );

        return $this;
    }

    public function attachRole( $role )
    {
        if (is_string( $role )) {
            $role = Role::where( 'label', $role )->firstOrFail();
        }

        $role->attachUser( $this );

        $this->rebuildPermissions();

        return $this;
    }

    public function detachRole( $role )
    {
        if (is_string( $role )) {
            $role = Role::where( 'label', $role )->firstOrFail();
        }

        $role->detachUser( $this );

        $this->rebuildPermissions();

        return $this;
    }

    public function hasRole( $role )
    {
        if (is_object( $role )) {
            $role = $role->label;
        }

        if (is_array( $role )) {
            foreach ($role as $instance) {
                if ($this->hasRole( $instance )) {
                    return true;
                }
            }

            return false;
        }

        return $this->roles->where( 'label', $role )->count() > 0;
    }

    public function hasPermission( $permission )
    {
        if (is_object( $permission )) {
            $permission = $permission->label;
        }

        if (is_array( $permission )) {
            foreach ($permission as $instance) {
                if ($this->hasPermission( $instance )) {
                    return true;
                }
            }

            return false;
        }

        return $this->permissions->where( 'label', $permission )->count() > 0;
    }

    public function scopeWithRole( Builder $builder, Role $role )
    {
        $builder->whereExists( function ( $query ) use ( $role ) {
            $query->select( $this->getConnection()->raw( 1 ) )
                ->from( 'role_user' )
                ->whereRaw( 'role_user.user_id = users.id' )
                ->where( 'role_user.role_id', $role->id );
        } );

        return $builder;
    }

    public function scopeWithPermission( Builder $builder, Permission $permission )
    {
        $builder->whereExists( function ( $query ) use ( $permission ) {
            $query->select( $this->getConnection()->raw( 1 ) )
                ->from( 'permission_user' )
                ->whereRaw( 'permission_user.user_id = users.id' )
                ->where( 'permission_user.permission_id', $permission->id );
        } );

        return $builder;
    }

    public function loadACLCache()
    {
        $this->setRelation( 'roles', value( function () {
            return Cache::tags( [ 'acl' ] )->rememberForever( $this->getACLCacheKey( 'roles' ), function () {
                return $this->roles()->get();
            } );
        } ) );

        $this->setRelation( 'permissions', value( function () {
            return Cache::tags( [ 'acl' ] )->rememberForever( $this->getACLCacheKey( 'permissions' ), function () {
                return $this->permissions()->get();
            } );
        } ) );
    }

    public static function clearACLCache()
    {
        Cache::tags( [ 'acl' ] )->flush();

        return true;
    }

    protected function getACLCacheKey( $label )
    {
        return 'acl.' . $this->id . '.' . $label;
    }
}
