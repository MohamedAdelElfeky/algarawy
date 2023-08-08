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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('title')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_location')->nullable();
            $table->string('company_type')->nullable();
            $table->string('company_link')->nullable();
            $table->string('company_logo')->nullable();

            $table->string('job_type')->nullable();
            $table->string('job_duration')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('job_status')->default(false);

            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('neighborhood_id')->nullable();
            $table->unsignedBigInteger('company_region_id')->nullable();
            $table->unsignedBigInteger('company_city_id')->nullable();
            $table->unsignedBigInteger('company_neighborhood_id')->nullable();

            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')->onDelete('cascade');
            $table->foreign('company_region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('company_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('company_neighborhood_id')->references('id')->on('neighborhoods')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
