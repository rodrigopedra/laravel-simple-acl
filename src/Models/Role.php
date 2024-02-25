<?php

namespace RodrigoPedra\LaravelSimpleACL\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $sort_index
 * @property string $label
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection<\RodrigoPedra\LaravelSimpleACL\Models\Permission> $permissions
 * @property \Illuminate\Database\Eloquent\Collection $users
 */
class Role extends Model
{
    use SoftDeletes;

    protected $connection = 'simple-acl';
    protected $table = 'roles';

    protected $fillable = [
        'label',
        'description',
        'sort_index',
    ];

    protected $casts = [
        'id' => 'integer',
        'sort_index' => 'integer',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\config('simple-acl.user-class'), 'role_user')->withTimestamps();
    }

    public function attachPermission(Permission $permission): static
    {
        $this->unsetRelation('permissions');
        $permission->unsetRelation('roles');
        $permission->unsetRelation('users');

        $this->permissions()->attach($permission->getKey());

        $this->rebuildUsersPermissions();

        return $this;
    }

    public function detachPermission(Permission $permission): static
    {
        $this->unsetRelation('permissions');
        $permission->unsetRelation('roles');
        $permission->unsetRelation('users');

        $this->permissions()->detach($permission->getKey());

        $this->rebuildUsersPermissions();

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model&\RodrigoPedra\LaravelSimpleACL\Concerns\HasACL  $user
     */
    public function attachUser(Model $user): static
    {
        $this->unsetRelation('users');

        $this->users()->sync([$user->getKey()], false);

        $user->rebuildPermissions();

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model&\RodrigoPedra\LaravelSimpleACL\Concerns\HasACL  $user
     */
    public function detachUser(Model $user): static
    {
        $this->unsetRelation('users');

        $this->users()->detach($user->getKey());

        $user->rebuildPermissions();

        return $this;
    }

    public function rebuildUsersPermissions(): static
    {
        $users = $this->users()->get();

        /** @var  \RodrigoPedra\LaravelSimpleACL\Concerns\HasACL $user */
        foreach ($users as $user) {
            $user->rebuildPermissions();
        }

        return $this;
    }

    public function scopeHasLabel(Builder $builder, string $label): void
    {
        $builder->where('label', $label);
    }

    public function scopeOrdered(Builder $builder): void
    {
        $builder->orderBy('sort_index')->orderBy('description');
    }
}
