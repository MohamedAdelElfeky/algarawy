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
            // $table->string('name');
            $table->text('description');
            $table->text('qualifications');
            $table->string('location');
            $table->text('contact_information');
            $table->string('photo')->nullable();
            $table->string('company_name');
            $table->string('company_location');
            $table->string('company_type');
            $table->string('company_link')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('job_type');
            $table->boolean('is_training');
            $table->boolean('is_full_time');
            $table->decimal('price', 10, 2);
            $table->boolean('job_status')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
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
