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
            ->create('permission_user', function (Blueprint $table) use ($userModel) {
                $userForeignKey = $userModel->getForeignKey();

                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger($userForeignKey);

                $table->primary([$userForeignKey, 'permission_id']);

                $table->timestamps();

                $table->foreign('permission_id')->references('id')->on('permissions');

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
        Schema::connection('simple-acl')->dropIfExists('permission_user');
    }
};
