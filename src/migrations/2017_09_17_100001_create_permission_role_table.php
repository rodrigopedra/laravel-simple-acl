<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRoleTable extends Migration
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
            ->create( 'permission_role', function ( Blueprint $table ) {
                $table->unsignedInteger( 'permission_id' );
                $table->unsignedInteger( 'role_id' );

                $table->primary( [ 'role_id', 'permission_id' ] );

                $table->timestamps();

                $table->foreign( 'permission_id' )->references( 'id' )->on( 'permissions' );
                $table->foreign( 'role_id' )->references( 'id' )->on( 'roles' );
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

        Schema::connection( $userModel->getConnectionName() )->dropIfExists( 'permission_role' );
    }
}
