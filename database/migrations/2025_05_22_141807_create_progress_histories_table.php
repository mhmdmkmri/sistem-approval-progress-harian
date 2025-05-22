<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_progress_histories_table.php
    public function up()
    {
        Schema::create('progress_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_id');
            $table->unsignedBigInteger('user_id'); // yang approve/reject
            $table->enum('action', ['approved_pm', 'approved_vp', 'rejected']);
            $table->text('comment')->nullable();
            $table->timestamp('action_at');

            $table->foreign('progress_id')->references('id')->on('progress')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_histories');
    }
};
