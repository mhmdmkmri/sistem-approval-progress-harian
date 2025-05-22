<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_progress_table.php
    public function up()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // officer yang input
            $table->unsignedBigInteger('project_id');
            $table->date('date');
            $table->integer('progress_percent');
            $table->string('evidence_path')->nullable();
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'approved_pm', 'approved_vp', 'rejected'])->default('pending');

            // Approval data
            $table->string('qr_code')->nullable(); // qr code approval

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
