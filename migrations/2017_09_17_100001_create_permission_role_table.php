<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('simple-acl')
            ->create('permission_role', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->primary(['role_id', 'permission_id']);

                $table->timestamps();

                $table->foreign('permission_id')->references('id')->on('permissions');
                $table->foreign('role_id')->references('id')->on('roles');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('simple-acl')->dropIfExists('permission_role');
    }
};
