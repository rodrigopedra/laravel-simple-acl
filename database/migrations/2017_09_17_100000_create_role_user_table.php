<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RodrigoPedra\LaravelSimpleACL\Models\Role;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $userClassName = \config('simple-acl.user-class');

        Schema::connection('simple-acl')
            ->create('role_user', function (Blueprint $table) use ($userClassName) {
                /** @var \Illuminate\Database\Eloquent\Model $userModel */
                $userModel = new $userClassName();

                $table->foreignIdFor(Role::class)->constrained();
                $table->foreignIdFor($userModel)->constrained();

                $table->primary([$userModel->getForeignKey(), 'role_id']);

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('simple-acl')->dropIfExists('role_user');
    }
};
