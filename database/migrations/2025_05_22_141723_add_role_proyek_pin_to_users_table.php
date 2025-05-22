<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_role_proyek_pin_to_users_table.php
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'officer', 'pm', 'vpqHSE'])->default('officer');
            $table->unsignedBigInteger('project_id')->nullable(); // terkait proyek
            $table->string('pin', 6)->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['role', 'project_id', 'pin']);
        });
    }
};
