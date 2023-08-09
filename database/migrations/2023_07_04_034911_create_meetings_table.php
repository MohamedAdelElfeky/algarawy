<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->datetime('datetime');
            $table->string('name')->nullable();
            $table->string('link')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
