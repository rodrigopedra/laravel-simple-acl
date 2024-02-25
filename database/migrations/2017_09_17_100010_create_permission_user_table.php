<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RodrigoPedra\LaravelSimpleACL\Models\Permission;

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
            ->create('permission_user', function (Blueprint $table) use ($userClassName) {
                /** @var \Illuminate\Database\Eloquent\Model $userModel */
                $userModel = new $userClassName();

                $table->foreignIdFor(Permission::class)->constrained();
                $table->foreignIdFor($userModel)->constrained();

                $table->primary([$userModel->getForeignKey(), 'permission_id']);

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
        Schema::connection('simple-acl')->dropIfExists('permission_user');
    }
};
