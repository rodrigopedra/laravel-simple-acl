<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::connection('simple-acl')
            ->create('roles', function (Blueprint $table) {
                $table->id();

                $table->unsignedSmallInteger('sort_index')->nullable();

                $table->string('label')->unique();
                $table->string('description')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('simple-acl')->dropIfExists('roles');
    }
};
