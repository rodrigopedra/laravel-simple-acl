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
 * @property \Illuminate\Database\Eloquent\Collection<\RodrigoPedra\LaravelSimpleACL\Models\Role> $roles
 * @property \Illuminate\Database\Eloquent\Collection $users
 */
class Permission extends Model
{
    use SoftDeletes;

    protected $connection = 'simple-acl';
    protected $table = 'permissions';

    protected $fillable = [
        'label',
        'description',
        'sort_index',
    ];

    protected $casts = [
        'id' => 'integer',
        'sort_index' => 'integer',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\config('simple-acl.user-class'), 'permission_user')->withTimestamps();
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
