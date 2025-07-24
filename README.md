# Laravel Simple ACL

## Requirements

- PHP 8.1+
- Laravel 9+

## Installation

```JSON
{
    "require": {
        "rodrigopedra/laravel-simple-acl": "^1.0"
    }
}
```

- Export configuration and set the project's User model class name
  - default is: `App\Models\User`
- Add `\RodrigoPedra\LaravelSimpleACL\Concerns\HasACL` trait to the project's User model
- Run migrations
- Optionally load the included Middleware
  - This will load and cache a logged in user's roles and permissions and define a gate to each permission

## Usage

Create roles and permissions through the included `RodrigoPedra\LaravelSimpleACL\Models\Role` and
`RodrigoPedra\LaravelSimpleACL\Models\Permission` Eloquent models.

Permissions are meant to be grouped into a role, you can create a database seeder, or migration for
your initial setup, for example:

```php
$addUsers = Permission::create([
    'label' => 'add-users',
    'description' => 'User is allowed to create new users',
    'sort_index' => 1,
]);

$removeUsers = Permission::create([
    'label' => 'remove-users',
    'description' => 'User is allowed to remove users',
    'sort_index' => 2,
]);

Role::create([
    'label' => 'admin',
    'sort_index' => 1,
])->attachPermission($addUsers)->attachPermission($removeUsers);

Role::create([
    'label' => 'leader',
    'sort_index' => 2,
])->attachPermission($addUsers);
```

You can then add or remove roles to individual users using the included trait's helper methods:

```php
$user->attachRole(Role::hasLabel('admin')->first());
$user->detachRole(Role::hasLabel('leader')->first());
```

If you add the `\RodrigoPedra\LaravelSimpleACL\Http\Middleware\LoadSimpleACL` middleware 
to your middleware stack, you can use Laravel's gate to check for permissions:

```php
if ($user->can('add-users')) {
    // do something
}
```

Even on blade views

```blade
@can('add-users')
    {{-- do something --}}
@endcan
```
