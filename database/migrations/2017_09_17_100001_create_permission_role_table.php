<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RodrigoPedra\LaravelSimpleACL\Models\Permission;
use RodrigoPedra\LaravelSimpleACL\Models\Role;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::connection('simple-acl')
            ->create('permission_role', function (Blueprint $table) {
                $table->foreignIdFor(Permission::class)->constrained();
                $table->foreignIdFor(Role::class)->constrained();

                $table->primary(['role_id', 'permission_id']);

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
        Schema::connection('simple-acl')->dropIfExists('permission_role');
    }
};
