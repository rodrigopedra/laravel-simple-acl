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
        $userClassName = config('simple-acl.user-class');

        /** @var \Illuminate\Database\Eloquent\Model $userModel */
        $userModel = new $userClassName();

        Schema::connection('simple-acl')
            ->create('role_user', function (Blueprint $table) use ($userModel) {
                $userForeignKey = $userModel->getForeignKey();

                $table->unsignedInteger('role_id');
                $table->unsignedBigInteger($userForeignKey);

                $table->primary([$userForeignKey, 'role_id']);

                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles');

                $table->foreign($userForeignKey)
                    ->references($userModel->getKeyName())
                    ->on($userModel->getTable());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('simple-acl')->dropIfExists('role_user');
    }
};
