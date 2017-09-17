<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userClassName = config( 'simple-acl.user_class' );

        /** @var \Illuminate\Database\Eloquent\Model $userModel */
        $userModel = new $userClassName;

        Schema::connection( $userModel->getConnectionName() )
            ->create( 'permissions', function ( Blueprint $table ) {
                $table->increments( 'id' );

                $table->unsignedSmallInteger( 'sort_index' )->nullable();

                $table->string( 'label' )->unique();
                $table->string( 'description' );

                $table->timestamps();
                $table->softDeletes();
            } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $userClassName = config( 'simple-acl.user_class' );

        /** @var \Illuminate\Database\Eloquent\Model $userModel */
        $userModel = new $userClassName;

        Schema::connection( $userModel->getConnectionName() )->dropIfExists( 'permissions' );
    }
}
