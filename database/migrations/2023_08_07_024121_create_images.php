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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('imageable_type'); // Polymorphic relationship type
            $table->unsignedBigInteger('imageable_id'); // Polymorphic relationship id
            $table->string('image_type')->nullable(); // You can use this field to categorize image types (project, courses, discounts, jobs, Service, etc.)
            $table->string('mime')->nullable(); // The MIME type of the image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
