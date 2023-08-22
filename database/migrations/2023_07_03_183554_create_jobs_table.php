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
            $table->longText('description')->nullable();
            $table->string('title')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_location')->nullable();
            $table->string('company_description')->nullable();
            $table->string('company_type')->nullable();
            $table->string('company_link')->nullable();
            $table->string('company_logo')->nullable();

            $table->string('job_type')->nullable();
            $table->string('job_duration')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            
            $table->boolean('job_status')->default(false);        
            $table->boolean('is_training')->nullable();        

            $table->timestamps();
         
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
