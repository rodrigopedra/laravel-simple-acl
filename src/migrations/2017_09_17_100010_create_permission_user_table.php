<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionUserTable extends Migration
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
            ->create( 'permission_user', function ( Blueprint $table ) use ( $userModel ) {
                $userForeignKey = $userModel->getForeignKey();

                $table->unsignedInteger( 'permission_id' );
                $table->unsignedInteger( $userForeignKey );

                $table->primary( [ $userForeignKey, 'permission_id' ] );

                $table->timestamps();

                $table->foreign( 'permission_id' )->references( 'id' )->on( 'permissions' );

                $table->foreign( $userForeignKey )
                    ->references( $userModel->getKeyName() )
                    ->on( $userModel->getTable() );
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

        Schema::connection( $userModel->getConnectionName() )->dropIfExists( 'permission_user' );
    }
}
