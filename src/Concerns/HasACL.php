<?php

namespace RodrigoPedra\LaravelSimpleACL\Concerns;

use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use RodrigoPedra\LaravelSimpleACL\Models\Permission;
use RodrigoPedra\LaravelSimpleACL\Models\Role;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property  \Illuminate\Database\Eloquent\Collection<\RodrigoPedra\LaravelSimpleACL\Models\Role> $roles
 * @property  \Illuminate\Database\Eloquent\Collection<\RodrigoPedra\LaravelSimpleACL\Models\Permission> $permissions
 */
trait HasACL
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function rebuildPermissions(): static
    {
        $this->unsetRelation('roles');
        $this->unsetRelation('permissions');

        Cache::forget($this->makeACLCacheKey('roles'));
        Cache::forget($this->makeACLCacheKey('permissions'));

        $this->load('roles.permissions');

        $permissionsKeys = $this->roles
            ->pluck('permissions')
            ->flatten(1)
            ->map(static fn (Permission $permission) => $permission->getKey());

        $this->permissions()->sync($permissionsKeys->unique()->all());

        return $this;
    }

    public function attachRole(Role|string $role): static
    {
        if (\is_string($role)) {
            $role = Role::query()->where('label', $role)->firstOrFail();
        }

        $role->attachUser($this);

        $this->rebuildPermissions();

        return $this;
    }

    public function detachRole(Role|string $role): static
    {
        if (\is_string($role)) {
            $role = Role::query()->where('label', $role)->firstOrFail();
        }

        $role->detachUser($this);

        $this->rebuildPermissions();

        return $this;
    }

    public function hasRole(Role|iterable|string $role): bool
    {
        if ($role instanceof Role) {
            $role = $role->label;
        }

        if (\is_string($role)) {
            return $this->roles->where('label', $role)->count() > 0;
        }

        foreach ($role as $instance) {
            if ($this->hasRole($instance)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission(Permission|iterable|string $permission): bool
    {
        if (\is_object($permission)) {
            $permission = $permission->label;
        }

        if (\is_string($permission)) {
            return $this->permissions->where('label', $permission)->count() > 0;
        }

        foreach ($permission as $instance) {
            if ($this->hasPermission($instance)) {
                return true;
            }
        }

        return false;
    }

    public function scopeWithRole(Builder $builder, Role $role): void
    {
        $builder->whereHas(
            'roles',
            static fn (BuilderContract $query) => $query->where('role_user.role_id', $role->getKey()),
        );
    }

    public function scopeWithPermission(Builder $builder, Permission $permission): void
    {
        $builder->whereHas(
            'permissions',
            static fn (BuilderContract $query) => $query->where('permission_user.permission_id', $permission->getKey()),
        );
    }

    public function loadACLCache(): void
    {
        $this->setRelation(
            'roles',
            Cache::rememberForever($this->makeACLCacheKey('roles'), fn () => $this->roles()->get()),
        );

        $this->setRelation(
            'permissions',
            Cache::rememberForever($this->makeACLCacheKey('permissions'), fn () => $this->permissions()->get()),
        );
    }

    protected function makeACLCacheKey(string $label): string
    {
        return 'acl.' . $this->getKey() . '.' . $label;
    }
}
